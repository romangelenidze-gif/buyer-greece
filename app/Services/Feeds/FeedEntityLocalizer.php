<?php

namespace App\Services\Feeds;

use App\Jobs\TranslateFeedEntitiesJob;
use App\Models\EntityTranslation;
use App\Models\FeedProduct;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class FeedEntityLocalizer
{
    private const CACHE_TTL = 86400;

    private const GREEK_STOP_ONLY = [
        'με', 'για', 'σε', 'απο', 'και', 'τησ', 'του', 'των', 'το', 'τα', 'η', 'ο', 'οι', 'ενα',
    ];

    /**
     * Выделяет греческую фразу из названия товара, оставляя латинское имя модели,
     * размеры и хвостовой артикул/цвет ("- 007355800 Λευκό") без изменений.
     *
     * @return array{prefix: string, model: string}|null
     */
    public static function splitGreekTitle(string $name): ?array
    {
        $name = trim((string) preg_replace('/\s+/u', ' ', $name));

        if (!preg_match('/[\x{0370}-\x{03FF}\x{1F00}-\x{1FFF}]/u', $name)) {
            return null;
        }

        // Отделяем хвостовой артикул и цвет вида " - 007355800 Λευκό", чтобы цвет не размножал префиксы
        $tail = '';
        $mainPart = $name;
        if (preg_match('/^(.*?)\s+(-\s*[A-Za-z0-9._\/-]+\s*.*)$/u', $name, $m)) {
            $mainPart = trim($m[1]);
            $tail = trim($m[2]);
        }

        $greekWords = [];
        $modelWords = [];

        foreach (explode(' ', $mainPart) as $word) {
            if ($word === '') {
                continue;
            }
            if (preg_match('/[\x{0370}-\x{03FF}\x{1F00}-\x{1FFF}]/u', $word)) {
                $greekWords[] = $word;
            } else {
                $modelWords[] = $word;
            }
        }

        if ($tail !== '') {
            $modelWords[] = $tail;
        }

        $prefix = trim(implode(' ', $greekWords));
        $model = trim(implode(' ', $modelWords));

        if ($prefix === '' || mb_strlen($prefix) > 70 || count($greekWords) > 7) {
            return null;
        }

        if (in_array(EntityTranslation::normalizeKey($prefix), self::GREEK_STOP_ONLY, true)) {
            return null;
        }

        return ['prefix' => $prefix, 'model' => $model];
    }

    /**
     * Локализация полного товара для детальной страницы из таблицы entity_translations (0 вызовов LLM).
     *
     * @return array{title: string, colour: ?string, specs: string[], categories: string[]}
     */
    public function localizeProduct(FeedProduct $product, string $locale): array
    {
        $rawCategories = array_values(array_filter(array_map('trim', explode(',', (string) $product->category_path))));
        $rawSpecs = array_values(array_filter(array_map('trim', (array) ($product->specs ?? []))));
        $rawColour = trim((string) ($product->colour ?? ''));

        if ($locale === 'el') {
            return [
                'title' => (string) $product->name,
                'colour' => $rawColour !== '' ? $rawColour : null,
                'specs' => $rawSpecs,
                'categories' => $rawCategories,
            ];
        }

        $title = (string) $product->name;
        $split = self::splitGreekTitle($title);
        if ($split !== null) {
            $prefixHash = EntityTranslation::hash($split['prefix']);
            $prefixMap = $this->getMap('title_prefix', [$prefixHash], $locale);
            if (isset($prefixMap[$prefixHash])) {
                $title = trim($prefixMap[$prefixHash] . ' ' . $split['model']);
            }
        }

        $colour = null;
        if ($rawColour !== '') {
            $colorParts = array_values(array_filter(array_map('trim', explode('/', $rawColour))));
            $colorHashes = array_map(fn ($c) => EntityTranslation::hash($c), $colorParts);
            $colorMap = $this->getMap('color', $colorHashes, $locale);

            $translatedParts = array_map(
                fn ($c) => $colorMap[EntityTranslation::hash($c)] ?? $c,
                $colorParts
            );
            $colour = implode(' / ', $translatedParts);
        }

        $specHashes = array_map(fn ($s) => EntityTranslation::hash($s), $rawSpecs);
        $specMap = $this->getMap('spec', $specHashes, $locale);
        $specs = array_map(
            fn ($s) => $specMap[EntityTranslation::hash($s)] ?? $s,
            $rawSpecs
        );

        $catHashes = array_map(fn ($c) => EntityTranslation::hash($c), $rawCategories);
        $catMap = $this->getMap('category_segment', $catHashes, $locale);
        $categories = array_map(
            fn ($c) => $catMap[EntityTranslation::hash($c)] ?? $c,
            $rawCategories
        );

        return [
            'title' => $title,
            'colour' => $colour,
            'specs' => $specs,
            'categories' => $categories,
        ];
    }

    /**
     * Пакетная локализация заголовков для карточек каталога/поиска (1 запрос на всю страницу, без N+1).
     */
    public function localizeCardTitles(array $cards, string $locale): array
    {
        if ($cards === [] || $locale === 'el') {
            return $cards;
        }

        $splits = [];
        $hashes = [];

        foreach ($cards as $i => $card) {
            $rawTitle = (string) ($card['title'] ?? '');
            $split = self::splitGreekTitle($rawTitle);
            if ($split !== null) {
                $h = EntityTranslation::hash($split['prefix']);
                $splits[$i] = ['hash' => $h, 'model' => $split['model']];
                $hashes[] = $h;
            }
        }

        if ($hashes === []) {
            return $cards;
        }

        $map = $this->getMap('title_prefix', array_values(array_unique($hashes)), $locale);

        foreach ($splits as $i => $info) {
            if (isset($map[$info['hash']])) {
                $cards[$i]['title'] = trim($map[$info['hash']] . ' ' . $info['model']);
            }
        }

        return $cards;
    }

    /**
     * Собирает компактную выжимку самых частотных непереведённых сущностей, отсортированную по популярности.
     *
     * @param array<string, int> $minCounts
     * @return array<string, string[]>
     */
    public function collectUntranslatedEntities(?string $feedKey = null, array $minCounts = []): array
    {
        $this->syncCategoryMappings($feedKey);

        $thresholds = array_merge([
            'category_segment' => 1,
            'color' => 4,
            'title_prefix' => 4,
            'spec' => 5,
        ], $minCounts);

        $query = FeedProduct::query()->where('is_active', true);
        if ($feedKey !== null) {
            $query->where('feed', $feedKey);
        }

        $candidates = [
            'category_segment' => [],
            'color' => [],
            'title_prefix' => [],
            'spec' => [],
        ];

        $counts = [
            'category_segment' => [],
            'color' => [],
            'title_prefix' => [],
            'spec' => [],
        ];

        $query->select(['id', 'name', 'colour', 'category_path', 'specs'])
            ->chunkById(1000, function ($rows) use (&$candidates, &$counts) {
                foreach ($rows as $row) {
                    foreach (explode(',', (string) $row->category_path) as $seg) {
                        $seg = trim($seg);
                        if ($seg !== '' && mb_strlen($seg) >= 2 && mb_strlen($seg) <= 80) {
                            $h = EntityTranslation::hash($seg);
                            $candidates['category_segment'][$h] ??= EntityTranslation::ucfirstMb(mb_strtolower($seg));
                            $counts['category_segment'][$h] = ($counts['category_segment'][$h] ?? 0) + 1;
                        }
                    }

                    foreach (explode('/', (string) $row->colour) as $colPart) {
                        $colPart = trim($colPart);
                        if ($colPart !== '' && mb_strlen($colPart) >= 2 && mb_strlen($colPart) <= 50 && !is_numeric($colPart)) {
                            $h = EntityTranslation::hash($colPart);
                            $candidates['color'][$h] ??= EntityTranslation::ucfirstMb(mb_strtolower($colPart));
                            $counts['color'][$h] = ($counts['color'][$h] ?? 0) + 1;
                        }
                    }

                    $split = self::splitGreekTitle((string) $row->name);
                    if ($split !== null) {
                        $h = EntityTranslation::hash($split['prefix']);
                        $cleanPrefix = EntityTranslation::ucfirstMb(mb_strtolower($split['prefix']));
                        if (!isset($candidates['title_prefix'][$h]) || preg_match('/[άέήίόύώ]/u', $split['prefix'])) {
                            $candidates['title_prefix'][$h] = $cleanPrefix;
                        }
                        $counts['title_prefix'][$h] = ($counts['title_prefix'][$h] ?? 0) + 1;
                    }

                    foreach ((array) ($row->specs ?? []) as $spec) {
                        $spec = trim((string) $spec);
                        if ($spec === '' || mb_strlen($spec) < 3 || mb_strlen($spec) > 120 || str_starts_with($spec, 'GTIN:')) {
                            continue;
                        }
                        $h = EntityTranslation::hash($spec);
                        $candidates['spec'][$h] ??= EntityTranslation::ucfirstMb(mb_strtolower($spec));
                        $counts['spec'][$h] = ($counts['spec'][$h] ?? 0) + 1;
                    }
                }
            });

        $missing = [];

        foreach ($candidates as $type => $byHash) {
            if ($byHash === []) {
                continue;
            }

            $minFreq = max(1, (int) ($thresholds[$type] ?? 1));

            $filteredCounts = array_filter($counts[$type], function ($c, $h) use ($type, $minFreq, $byHash) {
                $text = (string) ($byHash[$h] ?? '');

                if ($type === 'spec' && preg_match('/\d/u', $text)) {
                    return $c >= max(10, $minFreq * 2);
                }

                // Длинные фразы (5+ слов) переводим только если они повторяются минимум у 6 товаров
                if ($type === 'title_prefix' && substr_count($text, ' ') >= 4) {
                    return $c >= max(6, $minFreq);
                }

                return $c >= $minFreq;
            }, ARRAY_FILTER_USE_BOTH);

            arsort($filteredCounts);

            if ($filteredCounts === []) {
                continue;
            }

            $existingHashes = [];
            foreach (array_chunk(array_keys($filteredCounts), 500) as $chunk) {
                $found = EntityTranslation::query()
                    ->where('entity_type', $type)
                    ->whereIn('source_hash', $chunk)
                    ->pluck('source_hash')
                    ->all();
                $existingHashes = array_merge($existingHashes, $found);
            }

            $existingMap = array_fill_keys($existingHashes, true);
            $missing[$type] = [];

            foreach (array_keys($filteredCounts) as $hash) {
                if (!isset($existingMap[$hash])) {
                    $missing[$type][] = $byHash[$hash];
                }
            }
        }

        return array_filter($missing, fn ($list) => $list !== []);
    }

    public function dispatchMissingTranslations(?string $feedKey = null, bool $sync = false): int
    {
        $missing = $this->collectUntranslatedEntities($feedKey);
        $dispatched = 0;

        foreach ($missing as $type => $items) {
            foreach (array_chunk($items, 20) as $batch) {
                if ($sync) {
                    TranslateFeedEntitiesJob::dispatchSync($type, $batch);
                } else {
                    TranslateFeedEntitiesJob::dispatch($type, $batch);
                }
                $dispatched += count($batch);
            }
        }

        return $dispatched;
    }

    public function syncCategoryMappings(?string $feedKey = null): void
    {
        $categoriesBySlug = DB::table('categories')->pluck('id', 'slug')->all();
        if ($categoriesBySlug === []) {
            return;
        }

        $pathsQuery = DB::table('feed_products')
            ->where('is_active', true)
            ->whereNotNull('category_path')
            ->where('category_path', '!=', '');

        if ($feedKey !== null) {
            $pathsQuery->where('feed', $feedKey);
        }

        $distinctPaths = $pathsQuery
            ->select('feed', 'category_path')
            ->distinct()
            ->get();

        $now = now();

        foreach ($distinctPaths as $row) {
            $path = trim((string) $row->category_path);
            $hash = EntityTranslation::hash($path);
            $matchedCategoryId = $this->guessCategoryId($path, $categoriesBySlug);

            DB::table('feed_category_mappings')->upsert([[
                'feed' => $row->feed,
                'path_hash' => $hash,
                'raw_category_path' => $path,
                'category_id' => $matchedCategoryId,
                'updated_at' => $now,
                'created_at' => $now,
            ]], ['feed', 'path_hash'], ['raw_category_path']);

            if ($matchedCategoryId !== null) {
                DB::table('feed_products')
                    ->where('feed', $row->feed)
                    ->where('category_path', $path)
                    ->whereNull('category_id')
                    ->update(['category_id' => $matchedCategoryId]);
            }
        }
    }

    private function guessCategoryId(string $categoryPath, array $categoriesBySlug): ?int
    {
        foreach ((array) config('home_categories.tiles', []) as $tile) {
            $slug = (string) ($tile['slug'] ?? '');
            if (!isset($categoriesBySlug[$slug])) {
                continue;
            }

            $wherePaths = (array) ($tile['where']['category_path'] ?? []);
            foreach ($wherePaths as $needle) {
                if ($needle !== '' && mb_stripos($categoryPath, (string) $needle) !== false) {
                    return (int) $categoriesBySlug[$slug];
                }
            }
        }

        $top = trim(explode(',', $categoryPath)[0] ?? '');
        if ($top === 'Αξεσουάρ' && isset($categoriesBySlug['accessories'])) {
            return (int) $categoriesBySlug['accessories'];
        }

        return null;
    }

    /**
     * @param string[] $hashes
     * @return array<string, string>
     */
    private function getMap(string $entityType, array $hashes, string $locale): array
    {
        if ($hashes === []) {
            return [];
        }

        $out = [];
        $missing = [];

        foreach ($hashes as $h) {
            $cached = Cache::get("entity_tr:{$entityType}:{$locale}:{$h}");
            if (is_string($cached)) {
                $out[$h] = $cached;
            } else {
                $missing[] = $h;
            }
        }

        if ($missing !== []) {
            $rows = EntityTranslation::query()
                ->where('entity_type', $entityType)
                ->whereIn('source_hash', $missing)
                ->get();

            foreach ($rows as $row) {
                $val = $row->forLocale($locale);
                $out[$row->source_hash] = $val;
                Cache::put("entity_tr:{$entityType}:{$locale}:{$row->source_hash}", $val, self::CACHE_TTL);
            }
        }

        return $out;
    }
}