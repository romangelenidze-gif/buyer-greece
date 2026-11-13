<?php

namespace App\Services\Feeds;

use App\Models\FeedProduct;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Meilisearch\Client;

class FeedProductSearch
{
    public const DEFAULT_PER_PAGE = 24;

    public function __construct(protected FeedEntityLocalizer $localizer)
    {
    }

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
     * @return array{cards: array<int, array>, total: int, per_page: int, current_page: int, last_page: int}
     */
    public function searchPaginated(?string $feedKey, array $analysis, array $filters, string $userQuery = '', int $page = 1, int $perPage = self::DEFAULT_PER_PAGE): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(60, $perPage));
        $brand = $feedKey === null ? $this->resolveBrand($userQuery, $analysis) : null;

        if ($this->isMeilisearchEnabled()) {
            $meiliResult = $this->searchViaMeilisearch($feedKey, $brand, $analysis, $filters, $userQuery, $page, $perPage);
            if ($meiliResult !== null) {
                return $meiliResult;
            }
        }

        return $this->searchViaSql($feedKey, $brand, $analysis, $filters, $userQuery, $page, $perPage);
    }

    public function search(string $feedKey, array $analysis, array $filters, string $userQuery = '', ?int $limit = null): array
    {
        $perPage = $limit ?? (int) config('feeds.search_limit', self::DEFAULT_PER_PAGE);

        return $this->searchPaginated($feedKey, $analysis, $filters, $userQuery, 1, $perPage)['cards'];
    }

    protected function isMeilisearchEnabled(): bool
    {
        return config('scout.driver') === 'meilisearch'
            && !empty(config('scout.meilisearch.host'));
    }

    /**
     * @return array{cards: array<int, array>, total: int, per_page: int, current_page: int, last_page: int}|null
     */
    protected function searchViaMeilisearch(?string $feedKey, ?string $brand, array $analysis, array $filters, string $userQuery, int $page, int $perPage): ?array
    {
        try {
            $cfg = $feedKey ? (array) config("feeds.feeds.{$feedKey}", []) : [];
            $global = (array) config('feeds', []);
            $parsed = FeedText::parseQuery($userQuery, $cfg, $global);

            $filterClauses = ['is_active = true', 'in_stock = true'];

            if ($feedKey !== null) {
                $filterClauses[] = 'feed = "' . addslashes($feedKey) . '"';
            } elseif ($brand !== null) {
                $filterClauses[] = 'brand = "' . addslashes($brand) . '"';
            }

            if ($parsed['gender'] === 'men') {
                $filterClauses[] = 'gender IN ["men", "unisex"]';
            } elseif ($parsed['gender'] === 'women') {
                $filterClauses[] = 'gender IN ["women", "unisex"]';
            } elseif ($parsed['gender'] === 'kids') {
                $filterClauses[] = 'gender = "kids"';
            }

            if (!empty($filters['price_min'])) {
                $filterClauses[] = 'price >= ' . (float) $filters['price_min'];
            }
            if (!empty($filters['price_max'])) {
                $filterClauses[] = 'price <= ' . (float) $filters['price_max'];
            }

            $sort = $filters['sort'] ?? 'relevance';
            $meiliSort = match ($sort) {
                'price_asc' => ['price:asc', 'id:asc'],
                'price_desc' => ['price:desc', 'id:asc'],
                default => ['rating:desc', 'discount_percent:desc', 'id:asc'],
            };

            $searchQuery = trim($userQuery) !== ''
                ? trim($userQuery)
                : trim((string) ($analysis['query_en'] ?? ''));

            $client = new Client(
                (string) config('scout.meilisearch.host'),
                config('scout.meilisearch.key') ?: null
            );

            $indexName = (new FeedProduct())->searchableAs();
            $rawResult = $client->index($indexName)->search($searchQuery, [
                'filter' => implode(' AND ', $filterClauses),
                'sort' => $meiliSort,
                'matchingStrategy' => 'all',
                'offset' => ($page - 1) * $perPage,
                'limit' => $perPage,
            ]);

            $hits = is_object($rawResult) ? $rawResult->getHits() : ($rawResult['hits'] ?? []);
            $total = is_object($rawResult)
                ? (int) ($rawResult->getEstimatedTotalHits() ?? count($hits))
                : (int) ($rawResult['estimatedTotalHits'] ?? $rawResult['nbHits'] ?? count($hits));

            if ($hits === []) {
                return [
                    'cards' => [],
                    'total' => 0,
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'last_page' => 1,
                ];
            }

            $primaryIds = array_values(array_filter(array_map(fn ($h) => (int) ($h['id'] ?? 0), $hits)));
            $products = FeedProduct::query()
                ->whereIn('id', $primaryIds)
                ->get()
                ->keyBy('id');

            $groupKeys = [];
            foreach ($products as $p) {
                $groupKeys[] = $p->feed . ':' . ($p->model_code ?: $p->sku);
            }
            $groupKeys = array_values(array_unique($groupKeys));

            $colorwaysMap = [];
            if ($groupKeys !== []) {
                $colorwaysMap = DB::table('feed_products')
                    ->where('is_active', true)
                    ->where('in_stock', true)
                    ->whereIn(DB::raw("CONCAT(feed, ':', COALESCE(NULLIF(model_code, ''), sku))"), $groupKeys)
                    ->selectRaw("CONCAT(feed, ':', COALESCE(NULLIF(model_code, ''), sku)) as gk, COUNT(*) as cnt")
                    ->groupBy('gk')
                    ->pluck('cnt', 'gk')
                    ->all();
            }

            $cards = [];
            foreach ($primaryIds as $id) {
                /** @var FeedProduct|null $p */
                $p = $products->get($id);
                if (!$p) {
                    continue;
                }

                $gk = $p->feed . ':' . ($p->model_code ?: $p->sku);
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
                    'colorways_count' => (int) ($colorwaysMap[$gk] ?? 1),
                ];
            }

            $cards = $this->localizer->localizeCardTitles($cards, app()->getLocale());

            return [
                'cards' => $cards,
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => max(1, (int) ceil($total / $perPage)),
            ];
        } catch (\Throwable $e) {
            Log::warning('Meilisearch fallback to SQL: ' . $e->getMessage(), ['query' => $userQuery]);

            return null;
        }
    }

    /**
     * @return array{cards: array<int, array>, total: int, per_page: int, current_page: int, last_page: int}
     */
    protected function searchViaSql(?string $feedKey, ?string $brand, array $analysis, array $filters, string $userQuery, int $page, int $perPage): array
    {
        $cfg = $feedKey ? (array) config("feeds.feeds.{$feedKey}", []) : [];
        $global = (array) config('feeds', []);

        $rawCandidates = array_values(array_unique(array_filter([
            trim($userQuery),
            trim((string) ($analysis['query_en'] ?? '')),
            trim((string) ($analysis['query_el'] ?? '')),
        ])));

        if ($rawCandidates === []) {
            $rawCandidates = [''];
        }

        $parsedList = array_map(fn ($q) => FeedText::parseQuery($q, $cfg, $global), $rawCandidates);

        $gender = null;
        $categoryTop = null;
        $categoryPaths = [];

        foreach ($parsedList as $p) {
            $gender ??= $p['gender'];
            $categoryTop ??= $p['category_top'];
            $categoryPaths = array_values(array_unique(array_merge($categoryPaths, $p['category_paths'])));
        }

        $attempts = [];
        foreach ($parsedList as $p) {
            $attempts[] = [
                'groups' => $p['groups'],
                'gender' => $gender,
                'category_top' => $categoryTop,
                'category_paths' => $categoryPaths,
            ];
        }

        if ($categoryPaths !== []) {
            $attempts[] = [
                'groups' => $parsedList[0]['groups'],
                'gender' => $gender,
                'category_top' => $categoryTop,
                'category_paths' => [],
            ];
        }

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
            $grouped->orderByRaw('COALESCE(MAX(rating), -1) DESC')->orderByDesc('max_discount')->orderBy('primary_id');
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

        $cards = $this->localizer->localizeCardTitles($cards, app()->getLocale());

        return [
            'cards' => $cards,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => max(1, (int) ceil($total / $perPage)),
        ];
    }
}