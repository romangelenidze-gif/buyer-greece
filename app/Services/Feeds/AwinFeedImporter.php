<?php

namespace App\Services\Feeds;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AwinFeedImporter
{
    protected const BATCH = 500;
    protected const REQUIRED_COLUMNS_AWIN = ['aw_deep_link', 'product_name', 'merchant_product_id', 'search_price'];
    protected const REQUIRED_COLUMNS_LINKWISE = ['tracking_url', 'product_name', 'price'];

    protected const UPDATE_COLUMNS = [
        'model_code', 'brand', 'name', 'colour', 'category_path', 'category_top', 'gender', 'price',
        'old_price', 'discount_percent', 'currency', 'rating', 'in_stock', 'sizes', 'specs', 'image_url',
        'deep_link', 'merchant_url', 'delivery_cost', 'search_text', 'is_active', 'last_seen_at', 'updated_at',
    ];

    /**
     * @return array{total:int, imported:int, skipped:int, deactivated:int, deactivation_skipped:bool}
     */
    public function import(string $feedKey, ?string $source = null, int $limit = 0): array
    {
        $cfg = config("feeds.feeds.{$feedKey}");

        if (!is_array($cfg)) {
            throw new \InvalidArgumentException("Неизвестный фид: {$feedKey}");
        }

        if (($cfg['enabled'] ?? true) === false) {
            throw new \RuntimeException("Фид «{$feedKey}» отключён в config/feeds.php (enabled = false).");
        }

        $source = $source ?: ($cfg['url'] ?? null);

        if (!$source) {
            $envPrefix = strtoupper((string) ($cfg['network'] ?? 'AWIN'));
            throw new \RuntimeException("Не задан URL фида: добавьте {$envPrefix}_" . strtoupper($feedKey) . '_FEED_URL в .env или передайте --source.');
        }

        $startedAt = now()->toDateTimeString();
        [$path, $temporary] = $this->resolveSource($source);

        try {
            $stats = $this->process($feedKey, $cfg, $path, $startedAt, max(0, $limit));
            Cache::forget("home.category_chips.v2.{$feedKey}");
            Cache::forget('autocomplete.brands.all.v1');

            return $stats;
        } finally {
            if ($temporary) {
                @unlink($path);
            }
        }
    }

    protected function process(string $feedKey, array $cfg, string $path, string $startedAt, int $limit = 0): array
    {
        $handle = @fopen('compress.zlib://' . $path, 'r');

        if ($handle === false) {
            throw new \RuntimeException('Не удалось открыть файл фида.');
        }

        $network = strtolower((string) ($cfg['network'] ?? 'awin'));
        $requiredColumns = $network === 'linkwise'
            ? self::REQUIRED_COLUMNS_LINKWISE
            : self::REQUIRED_COLUMNS_AWIN;

        try {
            $header = fgetcsv($handle, 0, ',', '"', '');

            if (!is_array($header) || count($header) < 5) {
                throw new \RuntimeException('Файл не похож на CSV-фид (пустой заголовок). Проверьте URL и ключ.');
            }

            $header[0] = ltrim((string) $header[0], "\xEF\xBB\xBF");
            $header = array_map('trim', $header);

            foreach ($requiredColumns as $required) {
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
                    continue;
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

                if ($limit > 0 && ($imported + count($batch)) >= $limit) {
                    $imported += $this->flush($batch);
                    $batch = [];
                    break;
                }

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

        $minRatio = (float) config('feeds.min_import_ratio', 0.5);
        $deactivationSkipped = $limit > 0 || ($activeBefore > 0 && $imported < $activeBefore * $minRatio);
        $deactivated = 0;

        if ($deactivationSkipped) {
            Log::warning('Feed import: деактивация пропущена (тестовый лимит или малый объём фида)', [
                'feed' => $feedKey, 'imported' => $imported, 'active_before' => $activeBefore, 'limit' => $limit,
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
     * @return array{0: string, 1: bool}
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

    public static function mapRow(array $r, string $feedKey, array $cfg, string $seenAt): ?array
    {
        $network = strtolower((string) ($cfg['network'] ?? 'awin'));

        if ($network === 'linkwise') {
            return self::mapRowLinkwise($r, $feedKey, $cfg, $seenAt);
        }

        return self::mapRowAwin($r, $feedKey, $cfg, $seenAt);
    }

    protected static function mapRowLinkwise(array $r, string $feedKey, array $cfg, string $seenAt): ?array
    {
        $get = fn (string $key): string => trim((string) ($r[$key] ?? ''));

        $sku = $get('sku') ?: ($get('product_id') ?: $get('lw_product_id'));
        $name = html_entity_decode($get('product_name'), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $deepLink = $get('tracking_url');
        $price = self::decimal($get('price'));

        if ($sku === '' || $name === '' || $price === null || $price <= 0 || !filter_var($deepLink, FILTER_VALIDATE_URL)) {
            return null;
        }

        $old = self::decimal($get('full_price'));
        $oldPrice = ($old !== null && $old > $price) ? $old : null;
        $discount = $oldPrice !== null
            ? (int) max(1, min(99, round(($oldPrice - $price) / $oldPrice * 100)))
            : max(0, min(99, (int) $get('discount')));

        $rawStock = strtoupper($get('in_stock'));
        $inStock = in_array($rawStock, ['Y', '1', 'TRUE', 'YES'], true);

        $sizeList = self::list($get('size'));
        $sizes = array_map(fn (string $s) => [
            'size' => $s,
            'stock' => null,
            'ean' => $get('gtin') ?: null,
        ], $sizeList);

        $rawCategory = html_entity_decode($get('category'), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $catSegments = array_values(array_filter(
            array_map('trim', preg_split('/\s*(?:>|\/)\s*/u', $rawCategory) ?: []),
            fn ($s) => $s !== ''
        ));
        $categoryPath = implode(', ', $catSegments);
        $categoryTop = $catSegments[0] ?? '';

        $gender = self::resolveGender('', $categoryPath, $name);
        $colour = html_entity_decode($get('colour'), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $model = $get('model_name');

        $image = null;
        $imageCandidates = array_merge(
            [$get('image_url'), $get('thumb_url')],
            preg_split('/\s*[,|]\s*/', $get('extra_images')) ?: []
        );
        foreach ($imageCandidates as $candidate) {
            if ($candidate !== '' && filter_var($candidate, FILTER_VALIDATE_URL)) {
                $image = $candidate;
                break;
            }
        }

        $merchantUrl = filter_var($get('site_url'), FILTER_VALIDATE_URL) ?: null;
        if ($merchantUrl === null) {
            parse_str((string) parse_url($deepLink, PHP_URL_QUERY), $linkQuery);
            if (!empty($linkQuery['lnkurl']) && filter_var($linkQuery['lnkurl'], FILTER_VALIDATE_URL)) {
                $merchantUrl = (string) $linkQuery['lnkurl'];
            }
        }

        $specs = [];
        if ($get('gtin') !== '') {
            $specs[] = 'GTIN: ' . $get('gtin');
        }

        $brand = $get('brand_name') !== '' ? mb_substr($get('brand_name'), 0, 64) : ($cfg['brand'] ?? null);
        $searchText = ' ' . FeedText::normalize(implode(' ', array_filter([$brand, $name, $colour, $categoryPath, $sku, $model]))) . ' ';

        return [
            'feed' => $feedKey,
            'sku' => mb_substr($sku, 0, 64),
            'model_code' => $model !== '' ? mb_substr($model, 0, 64) : null,
            'brand' => $brand,
            'name' => mb_substr($name, 0, 255),
            'colour' => $colour !== '' ? mb_substr($colour, 0, 255) : null,
            'category_path' => $categoryPath !== '' ? mb_substr($categoryPath, 0, 500) : null,
            'category_top' => $categoryTop !== '' ? mb_substr($categoryTop, 0, 64) : null,
            'gender' => $gender,
            'price' => $price,
            'old_price' => $oldPrice,
            'discount_percent' => $discount,
            'currency' => strtoupper(substr($get('currency') ?: 'EUR', 0, 3)),
            'rating' => null,
            'in_stock' => $inStock ? 1 : 0,
            'sizes' => $sizes === [] ? null : json_encode($sizes, JSON_UNESCAPED_UNICODE),
            'specs' => $specs === [] ? null : json_encode($specs, JSON_UNESCAPED_UNICODE),
            'image_url' => $image,
            'deep_link' => $deepLink,
            'merchant_url' => $merchantUrl,
            'delivery_cost' => self::decimal($get('shipping')),
            'search_text' => $searchText,
            'is_active' => 1,
            'last_seen_at' => $seenAt,
            'created_at' => $seenAt,
            'updated_at' => $seenAt,
        ];
    }

    protected static function mapRowAwin(array $r, string $feedKey, array $cfg, string $seenAt): ?array
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
        $gender = self::resolveGender($get('Fashion:suitable_for'), $categoryPath, $name);
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

    protected static function resolveGender(string $suitableFor, string $categoryPath, string $name): ?string
    {
        foreach ([$suitableFor, $categoryPath, $name] as $idx => $source) {
            $clean = $source;
            if ($idx === 2) {
                $clean = (string) preg_replace('/\b(spider[\s\-]?man|iron[\s\-]?man|super[\s\-]?man|bat[\s\-]?man|pac[\s\-]?man|x[\s\-]?men)\b/iu', ' ', $clean);
            }

            $norm = FeedText::normalize($clean);
            if ($norm === '') {
                continue;
            }

            if (preg_match('/\bunisex\b/u', $norm)) {
                return 'unisex';
            }

            $tokens = explode(' ', $norm);
            $found = [];
            foreach ($tokens as $token) {
                $detected = FeedText::detectGender($token);
                if ($detected !== null) {
                    $found[$detected] = true;
                }
            }

            foreach (['kids', 'women', 'men'] as $priority) {
                if (isset($found[$priority])) {
                    return $priority;
                }
            }
        }

        return null;
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