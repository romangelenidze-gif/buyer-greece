<?php

namespace App\Services;

use App\Models\FeedProduct;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProductAutocompleteService
{
    private const CACHE_MINUTES = 15;
    private const MAX_TOTAL = 8;

    public function __construct(private CategoryMatcher $matcher)
    {
    }

    public function getSuggestions(string $query, string $locale = 'ru'): array
    {
        $typed = trim((string) preg_replace('/\s+/u', ' ', $query));

        if (mb_strlen($typed) < 2) {
            return [];
        }

        $key = 'autocomplete:v4:' . md5($locale . '|' . mb_strtolower($typed));

        try {
            return Cache::remember($key, now()->addMinutes(self::CACHE_MINUTES), fn () => $this->build($typed));
        } catch (\Throwable $e) {
            Log::warning('Autocomplete failed: ' . $e->getMessage(), ['query' => $typed]);

            return [];
        }
    }

    private function build(string $typed): array
    {
        $out = [];
        $matchedBrands = $this->brandSuggestions($typed);
        $restAfterBrand = trim($this->matcher->withoutBrands($typed));
        $primaryBrand = $matchedBrands[0] ?? $this->detectExactBrand($typed);

        // 1. Бренд + категория ("адидас жен...", "adidas кросс...")
        if ($primaryBrand !== null && $restAfterBrand !== '') {
            foreach ($this->matcher->suggest($restAfterBrand, 4) as $tile) {
                $combinedQuery = $primaryBrand . ' ' . $tile['label'];
                $out[] = [
                    'label' => $primaryBrand . ' — ' . $tile['label'],
                    'value' => $combinedQuery,
                    'badge' => __('Категория'),
                    'url' => route('product-search', ['q' => $combinedQuery]),
                    'image' => null,
                    'price' => null,
                ];
            }
        }

        // 2. Категории таксономии (на языке интерфейса)
        if ($out === [] && $restAfterBrand !== '') {
            foreach ($this->matcher->suggest($restAfterBrand, 5) as $tile) {
                $out[] = [
                    'label' => $tile['label'],
                    'value' => $tile['label'],
                    'badge' => __('Категория'),
                    'url' => $tile['url'],
                    'image' => null,
                    'price' => null,
                ];
            }
        }

        // 3. Бренды по всем активным фидам
        foreach ($matchedBrands as $brand) {
            $out[] = [
                'label' => $brand,
                'value' => $brand,
                'badge' => __('Бренд'),
                'url' => route('product-search', ['q' => $brand]),
                'image' => null,
                'price' => null,
            ];
        }

        // 4. Если введён только бренд ("adidas", "адидас"), добавляем популярные разделы этого бренда
        if ($primaryBrand !== null && $restAfterBrand === '') {
            foreach ($this->matcher->popularTiles(4) as $tile) {
                $combinedQuery = $primaryBrand . ' ' . $tile['label'];
                $out[] = [
                    'label' => $primaryBrand . ' — ' . $tile['label'],
                    'value' => $combinedQuery,
                    'badge' => __('Категория'),
                    'url' => route('product-search', ['q' => $combinedQuery]),
                    'image' => null,
                    'price' => null,
                ];
            }
        }

        // 5. Конкретные модели товаров (по латинице/цифрам)
        foreach ($this->modelSuggestions($restAfterBrand, $primaryBrand) as $model) {
            $out[] = [
                'label' => $model['name'],
                'value' => $model['name'],
                'badge' => $model['brand'] ?: __('Модель'),
                'url' => route('feed-product.show', ['feed' => $model['feed'], 'sku' => $model['sku']]),
                'image' => $model['image'],
                'price' => $model['price'],
            ];
        }

        $unique = [];
        $seen = [];
        foreach ($out as $item) {
            $k = mb_strtolower($item['label']);
            if (isset($seen[$k])) {
                continue;
            }
            $seen[$k] = true;
            $unique[] = $item;
        }

        return array_slice($unique, 0, self::MAX_TOTAL);
    }

    private function detectExactBrand(string $typed): ?string
    {
        $norm = $this->matcher->norm($typed);
        foreach ($this->allActiveBrands() as $brand) {
            $normBrand = $this->matcher->norm($brand);
            if ($normBrand !== '' && preg_match('/(?<![\p{L}\p{N}])' . preg_quote($normBrand, '/') . '(?![\p{L}\p{N}])/u', $norm)) {
                return $brand;
            }
        }

        foreach ((array) config('search_glossary.brands', []) as $alias => $canonical) {
            $normAlias = $this->matcher->norm((string) $alias);
            if ($normAlias !== '' && preg_match('/(?<![\p{L}\p{N}])' . preg_quote($normAlias, '/') . '(?![\p{L}\p{N}])/u', $norm)) {
                return (string) $canonical;
            }
        }

        return null;
    }

    /** @return string[] */
    private function allActiveBrands(): array
    {
        return Cache::remember('autocomplete.brands.all.v1', now()->addHours(6), function () {
            return FeedProduct::query()
                ->where('is_active', true)
                ->where('in_stock', true)
                ->whereNotNull('brand')
                ->distinct()
                ->orderBy('brand')
                ->pluck('brand')
                ->map(fn ($b) => trim((string) $b))
                ->filter()
                ->values()
                ->all();
        });
    }

    /** @return string[] */
    private function brandSuggestions(string $typed): array
    {
        $firstWord = explode(' ', $this->matcher->norm($typed))[0] ?? '';
        if (mb_strlen($firstWord) < 2) {
            return [];
        }

        $brands = $this->allActiveBrands();

        $aliases = [];
        foreach ((array) config('search_glossary.brands', []) as $alias => $canonical) {
            $normAlias = $this->matcher->norm((string) $alias);
            if ($normAlias !== '' && str_starts_with($normAlias, $firstWord)) {
                $aliases[] = $this->matcher->norm((string) $canonical);
            }
        }

        $found = [];
        foreach ($brands as $brand) {
            $normBrand = $this->matcher->norm($brand);
            $hit = str_starts_with($normBrand, $firstWord) || in_array($normBrand, $aliases, true);

            if ($hit) {
                $found[] = $brand;
            }

            if (count($found) >= 2) {
                break;
            }
        }

        return $found;
    }

    /** @return array<int, array{name: string, sku: string, feed: string, brand: string, image: ?string, price: ?string}> */
    private function modelSuggestions(string $text, ?string $brand): array
    {
        if (mb_strlen($text) < 2 || !preg_match('/[A-Za-z0-9]/', $text)) {
            return [];
        }

        $like = addcslashes($text, '%_\\');

        $query = FeedProduct::query()
            ->where('is_active', true)
            ->where('in_stock', true)
            ->where(function ($q) use ($like) {
                $q->where('name', 'like', $like . '%')
                    ->orWhere('name', 'like', '% ' . $like . '%')
                    ->orWhere('model_code', 'like', $like . '%');
            });

        if ($brand !== null) {
            $query->where('brand', $brand);
        }

        return $query
            ->selectRaw('name, MAX(feed) as feed, MAX(brand) as brand, MIN(sku) as sku, MIN(image_url) as image_url, MIN(price) as min_price')
            ->groupBy('name')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(4)
            ->get()
            ->map(fn ($row) => [
                'name' => (string) $row->name,
                'sku' => (string) $row->sku,
                'feed' => (string) $row->feed,
                'brand' => (string) ($row->brand ?? ''),
                'image' => $row->image_url ? (string) $row->image_url : null,
                'price' => is_numeric($row->min_price) ? '€' . number_format((float) $row->min_price, 2, '.', ' ') : null,
            ])
            ->all();
    }
}