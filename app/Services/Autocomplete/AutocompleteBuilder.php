<?php

namespace App\Services\Autocomplete;

use App\Models\AutocompleteEntry;
use App\Models\EntityTranslation;
use App\Models\FeedProduct;
use App\Services\FeedCategoryTiles;
use App\Services\Feeds\FeedEntityLocalizer;
use App\Services\Feeds\FeedText;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

class AutocompleteBuilder
{
    private const LOCALES = ['en', 'ru', 'ka', 'el'];

    public function __construct(private FeedCategoryTiles $categoryTiles)
    {
    }

    /**
     * Полная материализация таблицы autocomplete_entries по актуальным товарам в наличии
     * с сохранением накопленной популярности (popularity_hits).
     *
     * @return array{brands: int, categories: int, facets: int, models: int, total: int}
     */
    public function rebuild(): array
    {
        $now = now();
        $existingHits = AutocompleteEntry::query()
            ->pluck('popularity_hits', 'entry_key')
            ->all();

        $prefixTranslations = EntityTranslation::query()
            ->where('entity_type', 'title_prefix')
            ->get()
            ->keyBy('source_hash');

        $brandAliasesMap = $this->buildBrandAliasesMap();
        $rows = [];
        $stats = ['brands' => 0, 'categories' => 0, 'facets' => 0, 'models' => 0, 'total' => 0];

        // 1. Активные бренды
        $brandRows = FeedProduct::query()
            ->where('is_active', true)
            ->where('in_stock', true)
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->selectRaw("brand, COUNT(DISTINCT COALESCE(NULLIF(model_code, ''), sku)) as models_count")
            ->groupBy('brand')
            ->orderByDesc('models_count')
            ->get();

        $activeBrands = [];
        foreach ($brandRows as $bRow) {
            $brand = trim((string) $bRow->brand);
            $count = (int) $bRow->models_count;
            if ($brand === '' || $count <= 0) {
                continue;
            }

            $activeBrands[$brand] = $count;
            $key = 'brand:' . mb_strtolower($brand);
            $aliases = $brandAliasesMap[mb_strtolower($brand)] ?? [$brand];

            $rows[$key] = [
                'entry_key' => $key,
                'type' => 'brand',
                'title_en' => $brand,
                'title_ru' => $brand,
                'title_ka' => $brand,
                'title_el' => $brand,
                'badge_en' => 'Brand',
                'badge_ru' => 'Бренд',
                'badge_ka' => 'ბრენდი',
                'badge_el' => 'Μάρκα',
                'search_keywords' => $this->packKeywords($aliases),
                'url' => route('product-search', ['q' => $brand], false),
                'image_url' => null,
                'price' => null,
                'products_count' => $count,
                'popularity_hits' => (int) ($existingHits[$key] ?? 0),
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $stats['brands']++;
        }

        // 2. Категории таксономии (только те, где реально есть товары)
        $tiles = $this->categoryTiles->get();
        foreach ($tiles as $tile) {
            $slug = (string) ($tile['slug'] ?? '');
            $count = (int) ($tile['count'] ?? 0);
            $rawLabel = (string) ($tile['label'] ?? '');

            if ($slug === '' || $count <= 0 || $rawLabel === '') {
                continue;
            }

            $labels = $this->translateUiLabel($rawLabel);
            $key = 'cat:' . $slug;

            $rows[$key] = [
                'entry_key' => $key,
                'type' => 'category',
                'title_en' => $labels['en'],
                'title_ru' => $labels['ru'],
                'title_ka' => $labels['ka'],
                'title_el' => $labels['el'],
                'badge_en' => 'Category',
                'badge_ru' => 'Категория',
                'badge_ka' => 'კატეგორია',
                'badge_el' => 'Κατηγορία',
                'search_keywords' => $this->packKeywords(array_merge(array_values($labels), [$slug])),
                'url' => route('feed-category.show', ['slug' => $slug], false),
                'image_url' => null,
                'price' => null,
                'products_count' => $count,
                'popularity_hits' => (int) ($existingHits[$key] ?? 0),
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $stats['categories']++;

            // 3. Фасеты Бренд + Категория (только при наличии реальных товаров > 0)
            foreach ($activeBrands as $brand => $brandTotal) {
                $facetCount = (clone $this->categoryTiles->productsQuery($tile))
                    ->where('brand', $brand)
                    ->selectRaw("COUNT(DISTINCT COALESCE(NULLIF(model_code, ''), sku)) as cnt")
                    ->value('cnt');

                $facetCount = (int) $facetCount;
                if ($facetCount <= 0) {
                    continue;
                }

                $facetKey = 'facet:' . mb_strtolower($brand) . ':' . $slug;
                $aliases = $brandAliasesMap[mb_strtolower($brand)] ?? [$brand];

                $rows[$facetKey] = [
                    'entry_key' => $facetKey,
                    'type' => 'facet',
                    'title_en' => $brand . ' — ' . $labels['en'],
                    'title_ru' => $brand . ' — ' . $labels['ru'],
                    'title_ka' => $brand . ' — ' . $labels['ka'],
                    'title_el' => $brand . ' — ' . $labels['el'],
                    'badge_en' => 'Category',
                    'badge_ru' => 'Категория',
                    'badge_ka' => 'კატეგორია',
                    'badge_el' => 'Κατηγορία',
                    'search_keywords' => $this->packKeywords(array_merge($aliases, array_values($labels), [$slug])),
                    'url' => route('product-search', ['q' => $brand . ' ' . $labels['ru']], false),
                    'image_url' => null,
                    'price' => null,
                    'products_count' => $facetCount,
                    'popularity_hits' => (int) ($existingHits[$facetKey] ?? 0),
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $stats['facets']++;
            }
        }

        // 4. Уникальные модели (сгруппированы по model_code, данные берутся из самой дешёвой расцветки в наличии)
        $groupedModels = DB::table('feed_products')
            ->where('is_active', true)
            ->where('in_stock', true)
            ->selectRaw("feed, COALESCE(NULLIF(model_code, ''), sku) as group_key, MIN(id) as fallback_id, COUNT(*) as colorways_count")
            ->groupBy('feed', 'group_key')
            ->get();

        $cheapestByGroup = [];
        FeedProduct::query()
            ->where('is_active', true)
            ->where('in_stock', true)
            ->orderBy('price')
            ->orderBy('id')
            ->select(['id', 'feed', 'sku', 'model_code', 'brand', 'name', 'price', 'image_url'])
            ->chunk(2000, function ($products) use (&$cheapestByGroup) {
                foreach ($products as $p) {
                    $gKey = $p->feed . ':' . ($p->model_code ?: $p->sku);
                    if (!isset($cheapestByGroup[$gKey])) {
                        $cheapestByGroup[$gKey] = $p;
                    }
                }
            });

        $colorwaysCountMap = [];
        foreach ($groupedModels as $gm) {
            $colorwaysCountMap[$gm->feed . ':' . $gm->group_key] = (int) $gm->colorways_count;
        }

        foreach ($cheapestByGroup as $gKey => $product) {
            $brand = trim((string) ($product->brand ?? ''));
            $rawName = trim((string) $product->name);
            if ($rawName === '') {
                continue;
            }

            $titleEl = $rawName;
            $titleEn = $rawName;
            $titleRu = $rawName;
            $titleKa = $rawName;

            $split = FeedEntityLocalizer::splitGreekTitle($rawName);
            if ($split !== null) {
                $tr = $prefixTranslations->get(EntityTranslation::hash($split['prefix']));
                if ($tr !== null) {
                    $titleEn = trim($tr->forLocale('en') . ' ' . $split['model']);
                    $titleRu = trim($tr->forLocale('ru') . ' ' . $split['model']);
                    $titleKa = trim($tr->forLocale('ka') . ' ' . $split['model']);
                }
            }

            $entryKey = 'model:' . mb_strtolower($gKey);
            $aliases = $brand !== '' ? ($brandAliasesMap[mb_strtolower($brand)] ?? [$brand]) : [];

            $rows[$entryKey] = [
                'entry_key' => $entryKey,
                'type' => 'model',
                'title_en' => mb_substr($titleEn, 0, 255),
                'title_ru' => mb_substr($titleRu, 0, 255),
                'title_ka' => mb_substr($titleKa, 0, 255),
                'title_el' => mb_substr($titleEl, 0, 255),
                'badge_en' => $brand !== '' ? $brand : 'Model',
                'badge_ru' => $brand !== '' ? $brand : 'Модель',
                'badge_ka' => $brand !== '' ? $brand : 'მოდელი',
                'badge_el' => $brand !== '' ? $brand : 'Μοντέλο',
                'search_keywords' => $this->packKeywords(array_merge(
                    [$titleEn, $titleRu, $titleKa, $titleEl, (string) $product->model_code, (string) $product->sku],
                    $aliases
                )),
                'url' => route('feed-product.show', ['feed' => $product->feed, 'sku' => $product->sku], false),
                'image_url' => $product->image_url ? (string) $product->image_url : null,
                'price' => (float) $product->price,
                'products_count' => $colorwaysCountMap[$gKey] ?? 1,
                'popularity_hits' => (int) ($existingHits[$entryKey] ?? 0),
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $stats['models']++;
        }

        foreach (array_chunk(array_values($rows), 500) as $chunk) {
            DB::table('autocomplete_entries')->upsert(
                $chunk,
                ['entry_key'],
                [
                    'type',
                    'title_en',
                    'title_ru',
                    'title_ka',
                    'title_el',
                    'badge_en',
                    'badge_ru',
                    'badge_ka',
                    'badge_el',
                    'search_keywords',
                    'url',
                    'image_url',
                    'price',
                    'products_count',
                    'is_active',
                    'updated_at',
                ]
            );
        }

        if ($rows !== []) {
            AutocompleteEntry::query()
                ->where('updated_at', '<', $now)
                ->delete();
        }

        Cache::increment('autocomplete:cache_ver');
        $stats['total'] = count($rows);

        return $stats;
    }

    /**
     * @return array<string, string[]>
     */
    private function buildBrandAliasesMap(): array
    {
        $map = [];
        foreach ((array) config('search_glossary.brands', []) as $alias => $canonical) {
            $canonKey = mb_strtolower(trim((string) $canonical));
            if ($canonKey === '') {
                continue;
            }
            $map[$canonKey][] = (string) $canonical;
            $map[$canonKey][] = (string) $alias;
        }

        foreach ($map as $k => $list) {
            $map[$k] = array_values(array_unique($list));
        }

        return $map;
    }

    /**
     * @return array{en: string, ru: string, ka: string, el: string}
     */
    private function translateUiLabel(string $label): array
    {
        $out = [];
        foreach (self::LOCALES as $loc) {
            $tr = Lang::get($label, [], $loc);
            $out[$loc] = is_string($tr) && $tr !== '' ? $tr : $label;
        }

        return $out;
    }

    /**
     * @param string[] $parts
     */
    private function packKeywords(array $parts): string
    {
        $normalized = [];
        foreach ($parts as $part) {
            $norm = FeedText::normalize($part);
            if ($norm !== '') {
                $normalized[] = $norm;
            }
        }

        $joined = implode(' ', array_unique($normalized));

        return ' ' . trim($joined) . ' ';
    }
}