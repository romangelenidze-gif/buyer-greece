<?php

namespace App\Services;

use App\Services\Feeds\FeedProductSearch;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SmartProductSearchManager
{
    protected const CACHE_VERSION = 'v12';
    protected const CACHE_TTL = 3600; // 1 час

    public function __construct(
        protected GroqAiService $groqService,
        protected LocalQueryAnalyzer $localAnalyzer,
        protected FeedProductSearch $feedSearch
    ) {
    }

    public function search(string $userQuery, string $locale = 'ru', array $userFilters = [], int $page = 1): array
    {
        $startedAt = microtime(true);
        $userQuery = trim($userQuery);

        if ($userQuery === '') {
            return $this->emptyResult($userQuery, [], __('Введите запрос для поиска.'));
        }

        $targetLang = $this->resolveTargetLang($userQuery, $locale);
        $userFilters = $this->normalizeUserFilters($userFilters);
        $page = max(1, $page);

        $normalizedQuery = mb_strtolower(trim((string) preg_replace('/[^\p{L}\p{N}\s]+/u', '', $userQuery)));
        $cacheKey = 'buyer_greece_search_' . self::CACHE_VERSION . '_'
            . md5($normalizedQuery . '_' . $targetLang . '_p' . $page . '_' . json_encode($userFilters));

        $cached = Cache::get($cacheKey);
        if (is_array($cached)) {
            $this->logSearch($userQuery, $targetLang, $cached, $startedAt, 'result_cache');

            return $cached;
        }

        $result = $this->run($userQuery, $targetLang, $userFilters, $page);
        $this->logSearch($userQuery, $targetLang, $result, $startedAt);

        Cache::put($cacheKey, $result, self::CACHE_TTL);

        return $result;
    }

    protected function run(string $userQuery, string $targetLang, array $userFilters, int $page): array
    {
        // 1. Локальный разбор бюджета, сортировки и словаря (0 мс)
        $analysis = $this->localAnalyzer->analyze($userQuery) ?? [
            'intent' => 'product_search',
            'source_language' => $targetLang,
            'query_el' => '',
            'query_en' => '',
            'brand' => null,
            'filters' => [],
            'source' => 'taxonomy',
        ];

        $activeFilters = $this->mergeFilters($analysis['filters'] ?? [], $userFilters);
        $feedKey = $this->feedSearch->matchFeed($analysis, $userQuery);
        $perPage = (int) config('feeds.search_limit', 24);

        // 2. Прямой поиск по индексированной БД фидов (без LLM)
        $paginated = $this->feedSearch->searchPaginated($feedKey, $analysis, $activeFilters, $userQuery, $page, $perPage);

        // 3. Крайний fallback на LLM: только если БД вернула 0, запрос длинный (>= 2 слов) и LLM явно включена для fallback
        if ($paginated['total'] === 0 && config('services.groq.search_fallback', false) && count(explode(' ', $userQuery)) >= 2) {
            $aiAnalysis = $this->groqService->analyzeQuery($userQuery);
            if (empty($aiAnalysis['degraded'])) {
                $analysis = $aiAnalysis;
                $activeFilters = $this->mergeFilters($analysis['filters'] ?? [], $userFilters);
                $feedKey = $this->feedSearch->matchFeed($analysis, $userQuery);
                $paginated = $this->feedSearch->searchPaginated($feedKey, $analysis, $activeFilters, $userQuery, $page, $perPage);
            }
        }

        if ($paginated['total'] === 0) {
            return $this->emptyResult(
                $userQuery,
                $activeFilters,
                __('По вашему запросу ничего не найдено. Попробуйте изменить параметры поиска или ценовой диапазон.'),
                ['intent_analysis' => $analysis]
            );
        }

        return [
            'query_summary' => $userQuery,
            'recommendation' => __('Найдено моделей: ') . $paginated['total'],
            'products' => $paginated['cards'],
            'pagination' => [
                'total' => $paginated['total'],
                'per_page' => $paginated['per_page'],
                'current_page' => $paginated['current_page'],
                'last_page' => $paginated['last_page'],
            ],
            'active_filters' => $activeFilters,
            'intent_analysis' => $analysis,
            'search_meta' => [
                'query_used' => $userQuery,
                'language_used' => $targetLang,
                'fallback_used' => ($analysis['source'] ?? '') === 'groq',
                'brand_mismatch' => false,
                'source' => $feedKey ? 'feed:' . $feedKey : 'feeds:all',
            ],
        ];
    }

    protected function resolveTargetLang(string $userQuery, string $locale): string
    {
        $detected = GroqAiService::detectLanguage($userQuery);
        if (in_array($detected, ['ru', 'ka', 'el'], true)) {
            return $detected;
        }

        return in_array($locale, ['ru', 'ka', 'en', 'el'], true) ? $locale : 'en';
    }

    protected function normalizeUserFilters(array $filters): array
    {
        $out = [];
        $min = $filters['price_min'] ?? $filters['min_price'] ?? null;
        $max = $filters['price_max'] ?? $filters['max_price'] ?? null;

        if (is_numeric($min) && (float) $min > 0) {
            $out['price_min'] = (float) $min;
        }
        if (is_numeric($max) && (float) $max > 0) {
            $out['price_max'] = (float) $max;
        }
        if (!empty($filters['sort']) && in_array($filters['sort'], ['price_asc', 'price_desc'], true)) {
            $out['sort'] = $filters['sort'];
        }

        return $out;
    }

    protected function mergeFilters(array $aiFilters, array $userFilters): array
    {
        $merged = [
            'price_min' => $aiFilters['price_min'] ?? null,
            'price_max' => $aiFilters['price_max'] ?? null,
            'sort' => $aiFilters['sort'] ?? 'relevance',
        ];

        foreach (['price_min', 'price_max', 'sort'] as $key) {
            if (isset($userFilters[$key])) {
                $merged[$key] = $userFilters[$key];
            }
        }

        return $merged;
    }

    protected function logSearch(string $query, string $lang, array $result, float $startedAt, ?string $route = null): void
    {
        try {
            $analysis = $result['intent_analysis'] ?? [];
            Log::build([
                'driver' => 'daily',
                'path' => storage_path('logs/search.log'),
                'days' => 30,
            ])->info('search', [
                'q' => $query,
                'lang' => $lang,
                'route' => $route ?? ($analysis['source'] ?? 'taxonomy'),
                'products' => $result['pagination']['total'] ?? count($result['products'] ?? []),
                'ms' => (int) round((microtime(true) - $startedAt) * 1000),
            ]);
        } catch (\Throwable) {
        }
    }

    protected function emptyResult(string $userQuery, array $activeFilters, string $message, array $extra = []): array
    {
        return array_merge([
            'query_summary' => $userQuery,
            'recommendation' => $message,
            'products' => [],
            'pagination' => ['total' => 0, 'per_page' => 24, 'current_page' => 1, 'last_page' => 1],
            'active_filters' => $activeFilters,
        ], $extra);
    }
}