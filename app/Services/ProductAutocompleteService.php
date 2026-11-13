<?php

namespace App\Services;

use App\Models\AutocompleteEntry;
use App\Models\FeedProduct;
use App\Services\Feeds\FeedText;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Meilisearch\Client;

class ProductAutocompleteService
{
    private const CACHE_MINUTES = 30;
    private const MAX_TOTAL = 8;

    public function getSuggestions(string $query, string $locale = 'ru'): array
    {
        $typed = trim((string) preg_replace('/\s+/u', ' ', $query));
        if (mb_strlen($typed) < 2) {
            return [];
        }

        $ver = (int) Cache::get('autocomplete:cache_ver', 1);
        $cacheKey = "autocomplete:mat:v{$ver}:" . md5($locale . '|' . mb_strtolower($typed));

        try {
            return Cache::remember($cacheKey, now()->addMinutes(self::CACHE_MINUTES), fn () => $this->queryEntries($typed, $locale));
        } catch (\Throwable $e) {
            Log::warning('Materialized autocomplete failed: ' . $e->getMessage(), ['query' => $typed]);

            return [];
        }
    }

    private function queryEntries(string $typed, string $locale): array
    {
        static $hasTable = null;
        $hasTable ??= Schema::hasTable('autocomplete_entries');
        if (!$hasTable) {
            return [];
        }

        $norm = FeedText::normalize($typed);
        if ($norm === '') {
            return [];
        }

        $tokens = array_values(array_filter(explode(' ', $norm), fn ($t) => $t !== ''));
        if ($tokens === []) {
            return [];
        }

        $titleCol = match ($locale) {
            'ru' => 'title_ru',
            'ka' => 'title_ka',
            'el' => 'title_el',
            default => 'title_en',
        };

        $q = AutocompleteEntry::query()->where('is_active', true);

        foreach ($tokens as $tok) {
            $escaped = addcslashes($tok, '%_\\');
            $q->where('search_keywords', 'like', '% ' . $escaped . '%');
        }

        $firstEscaped = addcslashes($tokens[0], '%_\\');

        $entries = $q
            ->orderByRaw("CASE WHEN LOWER({$titleCol}) LIKE ? THEN 0 ELSE 1 END", [$firstEscaped . '%'])
            ->orderByRaw("CASE type WHEN 'brand' THEN 1 WHEN 'category' THEN 2 WHEN 'facet' THEN 3 ELSE 4 END")
            ->orderByDesc('popularity_hits')
            ->orderByDesc('products_count')
            ->orderBy('price')
            ->limit(20)
            ->get();

        $out = [];
        $seenLabels = [];
        $modelCount = 0;

        foreach ($entries as $entry) {
            if ($entry->type === 'model' && $modelCount >= 4) {
                continue;
            }

            $label = $entry->titleFor($locale);
            $normLabel = mb_strtolower(trim($label));
            if ($normLabel === '' || isset($seenLabels[$normLabel])) {
                continue;
            }

            $seenLabels[$normLabel] = true;
            if ($entry->type === 'model') {
                $modelCount++;
            }

            $value = $entry->type === 'facet'
                ? str_replace(' — ', ' ', $label)
                : $label;

            $url = $entry->type === 'facet'
                ? route('product-search', ['q' => $value])
                : url($entry->url);

            $out[] = [
                'label' => $label,
                'value' => $value,
                'badge' => $entry->badgeFor($locale),
                'url' => $url,
                'image' => $entry->image_url,
                'price' => $entry->price !== null ? '€' . number_format($entry->price, 2, '.', ' ') : null,
            ];

            if (count($out) >= self::MAX_TOTAL) {
                return $out;
            }
        }

        // Если из-за опечатки SQL-таблица дала мало подсказок — добираем модели из Meilisearch
        if ($modelCount < 4 && config('scout.driver') === 'meilisearch' && !empty(config('scout.meilisearch.host'))) {
            $this->appendMeilisearchSuggestions($typed, $locale, $titleCol, $out, $seenLabels, $modelCount);
        }

        return $out;
    }

    private function appendMeilisearchSuggestions(
        string $typed,
        string $locale,
        string $titleCol,
        array &$out,
        array &$seenLabels,
        int &$modelCount
    ): void {
        try {
            $client = new Client(
                (string) config('scout.meilisearch.host'),
                config('scout.meilisearch.key') ?: null
            );

            $indexName = (new FeedProduct())->searchableAs();
            $res = $client->index($indexName)->search($typed, [
                'filter' => 'is_active = true AND in_stock = true',
                'matchingStrategy' => 'all',
                'limit' => 6,
                'attributesToRetrieve' => ['id', 'brand', 'title_en', 'title_ru', 'title_ka', 'title_el'],
            ]);

            $hits = is_object($res) ? $res->getHits() : ($res['hits'] ?? []);
            if ($hits === []) {
                return;
            }

            $ids = array_values(array_filter(array_map(fn ($h) => (int) ($h['id'] ?? 0), $hits)));
            $products = FeedProduct::query()
                ->whereIn('id', $ids)
                ->get(['id', 'feed', 'sku', 'brand', 'name', 'price', 'image_url'])
                ->keyBy('id');

            foreach ($hits as $hit) {
                if ($modelCount >= 4 || count($out) >= self::MAX_TOTAL) {
                    break;
                }

                $id = (int) ($hit['id'] ?? 0);
                /** @var FeedProduct|null $product */
                $product = $products->get($id);
                if (!$product) {
                    continue;
                }

                $label = trim((string) ($hit[$titleCol] ?? $hit['title_en'] ?? $product->name));
                $normLabel = mb_strtolower($label);
                if ($normLabel === '' || isset($seenLabels[$normLabel])) {
                    continue;
                }

                $seenLabels[$normLabel] = true;
                $modelCount++;

                $out[] = [
                    'label' => $label,
                    'value' => $label,
                    'badge' => (string) ($product->brand ?: 'Model'),
                    'url' => route('feed-product.show', ['feed' => $product->feed, 'sku' => $product->sku]),
                    'image' => $product->image_url,
                    'price' => '€' . number_format((float) $product->price, 2, '.', ' '),
                ];
            }
        } catch (\Throwable $e) {
            Log::warning('Meilisearch autocomplete fallback failed: ' . $e->getMessage(), ['query' => $typed]);
        }
    }
}