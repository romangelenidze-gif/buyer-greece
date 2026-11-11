<?php

namespace App\Services\Feeds;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Импорт CSV-фида Awin (обычный или .gz) в таблицу feed_products.
 * Читает файл потоком (память не растёт), делает upsert пачками по (feed, sku)
 * и деактивирует товары, которых в свежем фиде больше нет.
 */
class AwinFeedImporter
{
    protected const BATCH = 500;
    protected const REQUIRED_COLUMNS = ['aw_deep_link', 'product_name', 'merchant_product_id', 'search_price'];

    protected const GENDERS = [
        'Άνδρες' => 'men',
        'Γυναίκες' => 'women',
        'Unisex' => 'unisex',
        'Παιδιά' => 'kids',
    ];

    protected const UPDATE_COLUMNS = [
        'model_code', 'brand', 'name', 'colour', 'category_path', 'category_top', 'gender', 'price',
        'old_price', 'discount_percent', 'currency', 'rating', 'in_stock', 'sizes', 'specs', 'image_url',
        'deep_link', 'merchant_url', 'delivery_cost', 'search_text', 'is_active', 'last_seen_at', 'updated_at',
    ];

    /**
     * @param string|null $source URL или путь к локальному .csv / .csv.gz; по умолчанию URL из config/feeds.php
     * @return array{total:int, imported:int, skipped:int, deactivated:int, deactivation_skipped:bool}
     */
    public function import(string $feedKey, ?string $source = null): array
    {
        $cfg = config("feeds.feeds.{$feedKey}");

        if (!is_array($cfg)) {
            throw new \InvalidArgumentException("Неизвестный фид: {$feedKey}");
        }

        $source = $source ?: ($cfg['url'] ?? null);

        if (!$source) {
            throw new \RuntimeException('Не задан URL фида: добавьте AWIN_' . strtoupper($feedKey) . '_FEED_URL в .env или передайте --source.');
        }

        $startedAt = now()->toDateTimeString();
        [$path, $temporary] = $this->resolveSource($source);

        try {
            return $this->process($feedKey, $cfg, $path, $startedAt);
        } finally {
            if ($temporary) {
                @unlink($path);
            }
        }
    }

    protected function process(string $feedKey, array $cfg, string $path, string $startedAt): array
    {
        $handle = @fopen('compress.zlib://' . $path, 'r'); // читает и gzip, и обычный CSV

        if ($handle === false) {
            throw new \RuntimeException('Не удалось открыть файл фида.');
        }

        try {
            $header = fgetcsv($handle, 0, ',', '"', '');

            if (!is_array($header) || count($header) < 5) {
                throw new \RuntimeException('Файл не похож на CSV-фид (пустой заголовок). Проверьте URL и ключ.');
            }

            $header[0] = ltrim((string) $header[0], "\xEF\xBB\xBF");
            $header = array_map('trim', $header);

            foreach (self::REQUIRED_COLUMNS as $required) {
                if (!in_array($required, $header, true)) {
                    throw new \RuntimeException("В фиде нет обязательной колонки {$required}.");
                }
            }

            $columns = count($header);
            $activeBefore = DB::table('feed_products')->where('feed', $feedKey)->where('is_active', true)->count();

            $total = 0;
            $imported = 0;
            $skipped = 0;
            $batch = [];

            while (($row = fgetcsv($handle, 0, ',', '"', '')) !== false) {
                if ($row === [null]) {
                    continue; // пустая строка
                }

                $total++;

                if (count($row) !== $columns) {
                    $row = array_pad(array_slice($row, 0, $columns), $columns, '');
                }

                $mapped = self::mapRow(array_combine($header, $row), $feedKey, $cfg, $startedAt);

                if ($mapped === null) {
                    $skipped++;
                    continue;
                }

                $batch[$mapped['sku']] = $mapped;

                if (count($batch) >= self::BATCH) {
                    $imported += $this->flush($batch);
                    $batch = [];
                }
            }

            $imported += $this->flush($batch);
        } finally {
            fclose($handle);
        }

        if ($imported === 0) {
            throw new \RuntimeException('В фиде не нашлось ни одного пригодного товара: база не изменена.');
        }

        // Если фид пришёл усечённым, не прячем из каталога то, чего просто не дочитали.
        $minRatio = (float) config('feeds.min_import_ratio', 0.5);
        $deactivationSkipped = $activeBefore > 0 && $imported < $activeBefore * $minRatio;
        $deactivated = 0;

        if ($deactivationSkipped) {
            Log::warning('Feed import: фид заметно меньше текущей базы, деактивация пропущена', [
                'feed' => $feedKey, 'imported' => $imported, 'active_before' => $activeBefore,
            ]);
        } else {
            $deactivated = DB::table('feed_products')
                ->where('feed', $feedKey)
                ->where('is_active', true)
                ->where('last_seen_at', '<', $startedAt)
                ->update(['is_active' => false, 'updated_at' => $startedAt]);
        }

        Log::info('Feed import finished', [
            'feed' => $feedKey, 'total' => $total, 'imported' => $imported, 'skipped' => $skipped, 'deactivated' => $deactivated,
        ]);

        return [
            'total' => $total,
            'imported' => $imported,
            'skipped' => $skipped,
            'deactivated' => $deactivated,
            'deactivation_skipped' => $deactivationSkipped,
        ];
    }

    protected function flush(array $batch): int
    {
        if ($batch === []) {
            return 0;
        }

        $rows = array_values($batch);

        DB::table('feed_products')->upsert($rows, ['feed', 'sku'], self::UPDATE_COLUMNS);

        return count($rows);
    }

    /**
     * @return array{0: string, 1: bool} [путь к файлу, файл временный]
     */
    protected function resolveSource(string $source): array
    {
        if (!preg_match('#^https?://#i', $source)) {
            if (!is_file($source)) {
                throw new \RuntimeException("Файл не найден: {$source}");
            }

            return [$source, false];
        }

        $tmp = tempnam(sys_get_temp_dir(), 'feed_');

        try {
            // Не пишем URL ни в логи, ни в сообщения: в нём ключ Awin.
            Http::timeout(300)->withOptions(['sink' => $tmp])->get($source)->throw();
        } catch (RequestException $e) {
            @unlink($tmp);
            throw new \RuntimeException('Не удалось скачать фид: HTTP ' . $e->response->status());
        } catch (ConnectionException) {
            @unlink($tmp);
            throw new \RuntimeException('Не удалось скачать фид: нет соединения с сервером.');
        }

        return [$tmp, true];
    }

    /**
     * Строка CSV -> строка таблицы (JSON-поля уже закодированы, потому что upsert не применяет касты).
     * Возвращает null, если строка непригодна (нет SKU, цены или партнёрской ссылки).
     */
    public static function mapRow(array $r, string $feedKey, array $cfg, string $seenAt): ?array
    {
        $get = fn (string $key): string => trim((string) ($r[$key] ?? ''));

        $sku = $get('merchant_product_id');
        $name = $get('product_name');
        $deepLink = $get('aw_deep_link');
        $price = self::decimal($get('search_price'));

        if ($sku === '' || $name === '' || $price === null || $price <= 0 || !filter_var($deepLink, FILTER_VALIDATE_URL)) {
            return null;
        }

        $old = self::decimal($get('product_price_old'));
        $oldPrice = ($old !== null && $old > $price) ? $old : null;
        $discount = $oldPrice !== null ? (int) max(1, min(99, round(($oldPrice - $price) / $oldPrice * 100))) : 0;

        // Размеры, остатки и EAN приходят параллельными списками через запятую.
        $sizeList = self::list($get('Fashion:size'));
        $stockList = self::list($get('stock_quantity'));
        $eanList = self::list($get('ean'));
        $sizes = [];

        foreach ($sizeList as $i => $size) {
            $sizes[] = [
                'size' => $size,
                'stock' => count($stockList) === count($sizeList) ? max(0, (int) $stockList[$i]) : null,
                'ean' => count($eanList) === count($sizeList) ? $eanList[$i] : null,
            ];
        }

        $hasStockInfo = $sizes !== [] && count($stockList) === count($sizeList);
        $inStock = $hasStockInfo
            ? (bool) array_filter($sizes, fn ($s) => ($s['stock'] ?? 0) > 0)
            : $get('in_stock') !== '0';

        $specs = array_values(array_filter(array_map('trim', explode('|', $get('specifications'))), fn ($s) => $s !== ''));

        $categoryPath = $get('merchant_category');
        $categoryTop = trim(explode(',', $categoryPath)[0] ?? '');
        $gender = self::GENDERS[$get('Fashion:suitable_for')] ?? null;
        $colour = $get('colour');
        $model = $get('mpn');
        $rating = self::decimal($get('rating'));

        $image = null;
        foreach (['merchant_image_url', 'alternate_image', 'aw_image_url'] as $col) {
            if (filter_var($get($col), FILTER_VALIDATE_URL)) {
                $image = $get($col);
                break;
            }
        }

        $searchText = ' ' . FeedText::normalize(implode(' ', [$name, $colour, $categoryPath, $sku, $model])) . ' ';

        return [
            'feed' => $feedKey,
            'sku' => mb_substr($sku, 0, 64),
            'model_code' => $model !== '' ? mb_substr($model, 0, 64) : null,
            'brand' => $get('brand_name') !== '' ? mb_substr($get('brand_name'), 0, 64) : ($cfg['brand'] ?? null),
            'name' => mb_substr($name, 0, 255),
            'colour' => $colour !== '' ? mb_substr($colour, 0, 255) : null,
            'category_path' => $categoryPath !== '' ? mb_substr($categoryPath, 0, 500) : null,
            'category_top' => $categoryTop !== '' ? mb_substr($categoryTop, 0, 64) : null,
            'gender' => $gender,
            'price' => $price,
            'old_price' => $oldPrice,
            'discount_percent' => $discount,
            'currency' => strtoupper(substr($get('currency') ?: 'EUR', 0, 3)),
            'rating' => ($rating !== null && $rating > 0) ? min(5.0, $rating) : null,
            'in_stock' => $inStock ? 1 : 0,
            'sizes' => $sizes === [] ? null : json_encode($sizes, JSON_UNESCAPED_UNICODE),
            'specs' => $specs === [] ? null : json_encode($specs, JSON_UNESCAPED_UNICODE),
            'image_url' => $image,
            'deep_link' => $deepLink,
            'merchant_url' => filter_var($get('merchant_deep_link'), FILTER_VALIDATE_URL) ?: null,
            'delivery_cost' => self::decimal($get('delivery_cost')),
            'search_text' => $searchText,
            'is_active' => 1,
            'last_seen_at' => $seenAt,
            'created_at' => $seenAt,
            'updated_at' => $seenAt,
        ];
    }

    protected static function decimal(string $value): ?float
    {
        $value = str_replace(',', '.', trim($value));

        return is_numeric($value) ? round((float) $value, 2) : null;
    }

    /** @return string[] */
    protected static function list(string $value): array
    {
        if ($value === '') {
            return [];
        }

        return array_map('trim', explode(',', $value));
    }
}
