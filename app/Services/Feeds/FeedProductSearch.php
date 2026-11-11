<?php

namespace App\Services\Feeds;

use App\Models\FeedProduct;

/**
 * Поиск по партнёрским фидам (таблица feed_products). Принимает тот же разбор запроса,
 * что и BestPrice (query_el / query_en / relaxed_el), и отдаёт карточки в формате выдачи BestPrice
 * плюс поля для детальной страницы.
 */
class FeedProductSearch
{
    protected const FETCH_ROWS = 200; // строк (расцветок) вытягиваем, затем схлопываем по моделям

    /**
     * Ключ фида, который обслуживает этот запрос (например 'adidas'), или null.
     */
    public function matchFeed(array $analysis, string $userQuery): ?string
    {
        $haystack = mb_strtolower(trim(implode(' ', [
            (string) ($analysis['brand'] ?? ''),
            $userQuery,
            (string) ($analysis['query_en'] ?? ''),
            (string) ($analysis['query_el'] ?? ''),
        ])));

        foreach ((array) config('feeds.feeds', []) as $key => $cfg) {
            foreach ((array) ($cfg['brand_aliases'] ?? []) as $alias) {
                if ($alias !== '' && str_contains($haystack, mb_strtolower($alias))) {
                    return (string) $key;
                }
            }

            foreach ((array) ($cfg['model_keywords'] ?? []) as $keyword) {
                if ($keyword !== '' && preg_match('/(?<![\p{L}\p{N}])' . preg_quote(mb_strtolower($keyword), '/') . '(?![\p{L}\p{N}])/u', $haystack)) {
                    return (string) $key;
                }
            }
        }

        return null;
    }

    /**
     * Товары брендов с собственным фидом (Adidas) из выдачи BestPrice убираем: они показываются только из фида.
     */
    public function stripOwnedBrands(array $items): array
    {
        return array_values(array_filter($items, fn ($item) => !$this->isOwnedBrandItem((array) $item)));
    }

    public function isOwnedBrandItem(array $item): bool
    {
        $haystack = mb_strtolower(trim((string) ($item['brand'] ?? '') . ' ' . (string) ($item['title'] ?? '')));

        if ($haystack === '') {
            return false;
        }

        foreach ((array) config('feeds.feeds', []) as $cfg) {
            foreach ((array) ($cfg['brand_aliases'] ?? []) as $alias) {
                if ($alias !== '' && str_contains($haystack, mb_strtolower($alias))) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param array $filters активные фильтры: price_min, price_max (EUR), sort (relevance|price_asc|price_desc)
     * @return array<int, array> карточки товаров (по одной на модель)
     */
    public function search(string $feedKey, array $analysis, array $filters, string $userQuery = '', ?int $limit = null): array
    {
        $cfg = (array) config("feeds.feeds.{$feedKey}", []);
        $global = (array) config('feeds', []);
        $limit ??= (int) config('feeds.search_limit', 12);

        // Те же попытки, что у BestPrice: язык, выбранный разбором -> второй язык -> упрощённый запрос.
        $queries = [];
        foreach ([$analysis['query_el'] ?? '', $analysis['query_en'] ?? '', $analysis['relaxed_el'] ?? ''] as $q) {
            $q = trim((string) $q);

            if ($q !== '') {
                $queries[mb_strtolower($q)] = $q;
            }
        }

        if ($queries === []) {
            $queries = [trim($userQuery)];
        }

        $parsed = array_map(fn ($q) => FeedText::parseQuery($q, $cfg, $global), array_values($queries));
        $gender = null;

        foreach ($parsed as $p) {
            $gender ??= $p['gender'];
        }

        $withConditions = array_values(array_filter($parsed, fn ($p) => $p['groups'] !== []));

        // Все слова отброшены (запрос «adidas»): показываем каталог целиком по порядку по умолчанию.
        $attempts = $withConditions === [] ? [['groups' => [], 'gender' => $gender]] : $withConditions;

        foreach ($attempts as $attempt) {
            $rows = $this->query($feedKey, $attempt['groups'], $gender, $filters);

            if ($rows->isNotEmpty()) {
                return $this->toCards($rows, $cfg, $limit);
            }
        }

        return [];
    }

    protected function query(string $feedKey, array $groups, ?string $gender, array $filters)
    {
        $query = FeedProduct::query()
            ->where('feed', $feedKey)
            ->where('is_active', true)
            ->where('in_stock', true);

        foreach ($groups as $alternatives) {
            $query->where(function ($w) use ($alternatives) {
                foreach ($alternatives as $stem) {
                    $w->orWhere('search_text', 'like', FeedText::likePattern($stem));
                }
            });
        }

        if ($gender === 'men') {
            $query->whereIn('gender', ['men', 'unisex']);
        } elseif ($gender === 'women') {
            $query->whereIn('gender', ['women', 'unisex']);
        } elseif ($gender === 'kids') {
            $query->where('gender', 'kids');
        }

        if (!empty($filters['price_min'])) {
            $query->where('price', '>=', (float) $filters['price_min']);
        }

        if (!empty($filters['price_max'])) {
            $query->where('price', '<=', (float) $filters['price_max']);
        }

        $sort = $filters['sort'] ?? 'relevance';

        if ($sort === 'price_asc') {
            $query->orderBy('price');
        } elseif ($sort === 'price_desc') {
            $query->orderByDesc('price');
        } else {
            // Без данных о популярности: сначала товары с оценками, затем с большей скидкой.
            $query->orderByRaw('rating IS NULL')->orderByDesc('rating')->orderByDesc('discount_percent');
        }

        return $query->orderBy('id')->limit(self::FETCH_ROWS)->get();
    }

    protected function toCards($rows, array $cfg, int $limit): array
    {
        $store = (string) ($cfg['store_name'] ?? ($cfg['label'] ?? 'Store'));
        $byModel = [];

        foreach ($rows as $product) {
            $key = $product->model_code ?: $product->sku;

            if (!isset($byModel[$key])) {
                $byModel[$key] = ['product' => $product, 'colorways' => 0];
            }

            $byModel[$key]['colorways']++;
        }

        $cards = [];

        foreach (array_slice($byModel, 0, $limit) as $entry) {
            /** @var FeedProduct $p */
            $p = $entry['product'];
            $formatted = '€' . number_format($p->price, 2, '.', ' ');

            // Основа карточки та же, что на главной ($p->toCard()); ниже только поля, нужные поиску и сортировке.
            $cards[] = $p->toCard() + [
                'original_title' => $p->name,
                'merchant' => $store,
                'price_from' => $p->price,
                'price_formatted' => $formatted,
                'price_note' => $formatted,
                'formatted_price' => $formatted,
                'offers_count' => 1,
                'rating_count' => null,
                'colorways_count' => $entry['colorways'],
            ];
        }

        return $cards;
    }
}
