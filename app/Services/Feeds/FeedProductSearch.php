<?php

namespace App\Services\Feeds;

use App\Models\FeedProduct;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class FeedProductSearch
{
    public const DEFAULT_PER_PAGE = 24;

    public function matchFeed(array $analysis, string $userQuery): ?string
    {
        $haystack = FeedText::normalize(implode(' ', [
            (string) ($analysis['brand'] ?? ''),
            $userQuery,
            (string) ($analysis['query_en'] ?? ''),
            (string) ($analysis['query_el'] ?? ''),
        ]));

        foreach ((array) config('feeds.feeds', []) as $key => $cfg) {
            foreach ((array) ($cfg['brand_aliases'] ?? []) as $alias) {
                if ($alias !== '' && str_contains($haystack, FeedText::normalize((string) $alias))) {
                    return (string) $key;
                }
            }

            foreach ((array) ($cfg['model_keywords'] ?? []) as $keyword) {
                $normKw = FeedText::normalize((string) $keyword);
                if ($normKw !== '' && preg_match('/(?<![\p{L}\p{N}])' . preg_quote($normKw, '/') . '(?![\p{L}\p{N}])/u', $haystack)) {
                    return (string) $key;
                }
            }
        }

        return null;
    }

    public function resolveBrand(string $userQuery, array $analysis = []): ?string
    {
        if (!empty($analysis['brand'])) {
            return (string) $analysis['brand'];
        }

        $normQuery = FeedText::normalize($userQuery);
        foreach ((array) config('search_glossary.brands', []) as $alias => $canonical) {
            $normAlias = FeedText::normalize((string) $alias);
            $normCanon = FeedText::normalize((string) $canonical);
            if (($normAlias !== '' && preg_match('/(?<![\p{L}\p{N}])' . preg_quote($normAlias, '/') . '(?![\p{L}\p{N}])/u', $normQuery))
                || ($normCanon !== '' && preg_match('/(?<![\p{L}\p{N}])' . preg_quote($normCanon, '/') . '(?![\p{L}\p{N}])/u', $normQuery))) {
                return (string) $canonical;
            }
        }

        return null;
    }

    /**
     * Поиск с пагинацией и сортировкой по всей таблице feed_products.
     *
     * @return array{cards: array<int, array>, total: int, per_page: int, current_page: int, last_page: int}
     */
    public function searchPaginated(?string $feedKey, array $analysis, array $filters, string $userQuery = '', int $page = 1, int $perPage = self::DEFAULT_PER_PAGE): array
    {
        $cfg = $feedKey ? (array) config("feeds.feeds.{$feedKey}", []) : [];
        $global = (array) config('feeds', []);
        $page = max(1, $page);
        $perPage = max(1, min(60, $perPage));

        $brand = $feedKey === null ? $this->resolveBrand($userQuery, $analysis) : null;

        // Разбираем исходный запрос пользователя + обогащённые варианты (если были)
        $rawCandidates = array_values(array_unique(array_filter([
            trim($userQuery),
            trim((string) ($analysis['query_en'] ?? '')),
            trim((string) ($analysis['query_el'] ?? '')),
        ])));

        if ($rawCandidates === []) {
            $rawCandidates = [''];
        }

        $parsedList = array_map(fn ($q) => FeedText::parseQuery($q, $cfg, $global), $rawCandidates);

        // Собираем объединённые структурные фильтры (пол, верхняя категория, подкатегория)
        $gender = null;
        $categoryTop = null;
        $categoryPaths = [];

        foreach ($parsedList as $p) {
            $gender ??= $p['gender'];
            $categoryTop ??= $p['category_top'];
            $categoryPaths = array_values(array_unique(array_merge($categoryPaths, $p['category_paths'])));
        }

        // Формируем список попыток от самой точной к более широкой
        $attempts = [];
        foreach ($parsedList as $p) {
            $attempts[] = [
                'groups' => $p['groups'],
                'gender' => $gender,
                'category_top' => $categoryTop,
                'category_paths' => $categoryPaths,
            ];
        }

        // Fallback 1: если задана узкая подкатегория и текстовые слова, пробуем без сужения category_paths
        if ($categoryPaths !== []) {
            $attempts[] = [
                'groups' => $parsedList[0]['groups'],
                'gender' => $gender,
                'category_top' => $categoryTop,
                'category_paths' => [],
            ];
        }

        // Fallback 2: если не было ни слов, ни категорий (например запрос просто "adidas"), отдаём весь каталог бренда
        if ($parsedList[0]['groups'] === [] && $categoryTop === null && $categoryPaths === []) {
            $attempts[] = [
                'groups' => [],
                'gender' => $gender,
                'category_top' => null,
                'category_paths' => [],
            ];
        }

        $seenSignatures = [];
        foreach ($attempts as $attempt) {
            $sig = json_encode($attempt);
            if (isset($seenSignatures[$sig])) {
                continue;
            }
            $seenSignatures[$sig] = true;

            $result = $this->executePaginatedQuery($feedKey, $brand, $attempt, $filters, $page, $perPage);
            if ($result['total'] > 0) {
                return $result;
            }
        }

        return [
            'cards' => [],
            'total' => 0,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => 1,
        ];
    }

    /** Обратная совместимость со старым вызовом search(). */
    public function search(string $feedKey, array $analysis, array $filters, string $userQuery = '', ?int $limit = null): array
    {
        $perPage = $limit ?? (int) config('feeds.search_limit', self::DEFAULT_PER_PAGE);

        return $this->searchPaginated($feedKey, $analysis, $filters, $userQuery, 1, $perPage)['cards'];
    }

    protected function executePaginatedQuery(?string $feedKey, ?string $brand, array $attempt, array $filters, int $page, int $perPage): array
    {
        $base = FeedProduct::query()
            ->where('is_active', true)
            ->where('in_stock', true);

        if ($feedKey !== null) {
            $base->where('feed', $feedKey);
        } elseif ($brand !== null) {
            $base->where('brand', $brand);
        }

        if ($attempt['gender'] === 'men') {
            $base->whereIn('gender', ['men', 'unisex']);
        } elseif ($attempt['gender'] === 'women') {
            $base->whereIn('gender', ['women', 'unisex']);
        } elseif ($attempt['gender'] === 'kids') {
            $base->where('gender', 'kids');
        }

        if (!empty($attempt['category_top'])) {
            $base->where('category_top', $attempt['category_top']);
        }

        if (!empty($attempt['category_paths'])) {
            $paths = $attempt['category_paths'];
            $base->where(function (Builder $q) use ($paths) {
                foreach ($paths as $path) {
                    $q->orWhere('category_path', 'like', '%' . $path . '%');
                }
            });
        }

        foreach ($attempt['groups'] as $alternatives) {
            $base->where(function (Builder $w) use ($alternatives) {
                foreach ($alternatives as $stem) {
                    $w->orWhere('search_text', 'like', FeedText::likePattern($stem));
                }
            });
        }

        if (!empty($filters['price_min'])) {
            $base->where('price', '>=', (float) $filters['price_min']);
        }

        if (!empty($filters['price_max'])) {
            $base->where('price', '<=', (float) $filters['price_max']);
        }

        // Группируем по моделям прямо в MariaDB, чтобы сортировка и пагинация работали по всему каталогу
        $grouped = (clone $base)
            ->selectRaw("feed, COALESCE(NULLIF(model_code, ''), sku) as group_key, MIN(id) as primary_id, MIN(price) as min_price, MAX(rating) as max_rating, MAX(discount_percent) as max_discount, COUNT(*) as colorways_count")
            ->groupBy('feed', 'group_key');

        $total = DB::query()->fromSub($grouped, 'g')->count();

        if ($total === 0) {
            return [
                'cards' => [],
                'total' => 0,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => 1,
            ];
        }

        $sort = $filters['sort'] ?? 'relevance';
        if ($sort === 'price_asc') {
            $grouped->orderBy('min_price');
        } elseif ($sort === 'price_desc') {
            $grouped->orderByDesc('min_price');
        } else {
            $grouped->orderByRaw('max_rating IS NULL')->orderByDesc('max_rating')->orderByDesc('max_discount')->orderBy('primary_id');
        }

        $rows = $grouped
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        $primaryIds = $rows->pluck('primary_id')->all();
        $colorwaysMap = $rows->pluck('colorways_count', 'primary_id')->all();

        $products = FeedProduct::query()
            ->whereIn('id', $primaryIds)
            ->get()
            ->keyBy('id');

        $cards = [];
        foreach ($primaryIds as $id) {
            /** @var FeedProduct|null $p */
            $p = $products->get($id);
            if (!$p) {
                continue;
            }

            $store = (string) config("feeds.feeds.{$p->feed}.store_name", $p->brand ?: $p->feed);
            $formatted = '€' . number_format($p->price, 2, '.', ' ');

            $cards[] = $p->toCard() + [
                'original_title' => $p->name,
                'merchant' => $store,
                'price_from' => $p->price,
                'price_formatted' => $formatted,
                'price_note' => $formatted,
                'formatted_price' => $formatted,
                'offers_count' => 1,
                'rating_count' => null,
                'colorways_count' => (int) ($colorwaysMap[$id] ?? 1),
            ];
        }

        return [
            'cards' => $cards,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => max(1, (int) ceil($total / $perPage)),
        ];
    }
}