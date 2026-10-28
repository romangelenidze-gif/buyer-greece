<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Оркестратор умного поиска:
 *   запрос -> Groq (структурированный разбор) -> [уточнение для широких запросов]
 *   -> ОДИН запрос в BestPrice MCP (8 результатов) -> перевод названий на язык пользователя.
 *
 * Дополнительные запросы к MCP делаются только если предыдущий вернул пустую выдачу
 * (другой язык запроса, затем упрощённый запрос). ProductSearchPlanner больше не нужен.
 */
class SmartProductSearchManager
{
    protected const CACHE_VERSION = 'v9';
    protected const CACHE_TTL = 10800; // 3 часа
    protected const LIMIT = 8;

    protected GroqAiService $groqService;
    protected BestPriceMcpService $bestPrice;
    protected LocalQueryAnalyzer $localAnalyzer;

    public function __construct(
        GroqAiService $groqService,
        BestPriceMcpService $bestPrice,
        LocalQueryAnalyzer $localAnalyzer
    ) {
        $this->groqService = $groqService;
        $this->bestPrice = $bestPrice;
        $this->localAnalyzer = $localAnalyzer;
    }

    public function search(string $userQuery, string $locale = 'ru', array $userFilters = []): array
    {
        $startedAt = microtime(true);
        $userQuery = trim($userQuery);

        if ($userQuery === '') {
            return $this->emptyResult($userQuery, [], __('Введите запрос для поиска.'));
        }

        $targetLang = $this->resolveTargetLang($userQuery, $locale);
        $userFilters = $this->normalizeUserFilters($userFilters);

        $normalizedQuery = mb_strtolower(trim((string) preg_replace('/[^\p{L}\p{N}\s]+/u', '', $userQuery)));
        $cacheKey = 'buyer_greece_search_' . self::CACHE_VERSION . '_'
            . md5($normalizedQuery . '_' . $targetLang . '_' . json_encode($userFilters));

        $cache = Cache::store('file');
        $cached = $cache->get($cacheKey);

        if (is_array($cached)) {
            $this->logSearch($userQuery, $targetLang, $cached, $startedAt, 'result_cache');

            return $cached;
        }

        $result = $this->run($userQuery, $targetLang, $userFilters);
        $this->logSearch($userQuery, $targetLang, $result, $startedAt);

        // Сбои (LLM/MCP недоступны) не кэшируем, чтобы они не залипали на 3 часа.
        $noCache = !empty($result['_no_cache']);
        unset($result['_no_cache']);

        if (!$noCache) {
            $cache->put($cacheKey, $result, self::CACHE_TTL);
        }

        return $result;
    }

    protected function run(string $userQuery, string $targetLang, array $userFilters): array
    {
        // Сначала бесплатный локальный разбор (словарь, бюджет, латиница); LLM только если он не уверен.
        $analysis = $this->localAnalyzer->analyze($userQuery) ?? $this->groqService->analyzeQuery($userQuery);
        $activeFilters = $this->mergeFilters($analysis['filters'] ?? [], $userFilters);

        // LLM недоступна, а запрос на ru/ka: сырой текст в греческом каталоге ничего не найдёт.
        if (!empty($analysis['degraded']) && in_array($analysis['source_language'] ?? 'en', ['ru', 'ka'], true)) {
            Log::warning('SmartProductSearch: query analysis degraded, search skipped', ['query' => $userQuery]);

            return $this->emptyResult(
                $userQuery,
                $activeFilters,
                __('Не удалось обработать запрос. Попробуйте ещё раз чуть позже.'),
                ['_no_cache' => true, 'intent_analysis' => $analysis]
            );
        }

        // Широкий запрос ("часы", "кроссовки"): сначала уточняем, а не показываем случайную выдачу.
        if (
            !empty($analysis['is_broad'])
            && !empty($analysis['clarification'])
            && empty($userFilters['skip_clarification'])
        ) {
            return [
                'query_summary' => $userQuery,
                'needs_clarification' => true,
                'clarification' => $analysis['clarification'],
                'recommendation' => $analysis['clarification']['question'],
                'products' => [],
                'active_filters' => $activeFilters,
                'intent_analysis' => $analysis,
            ];
        }

        $mcpOptions = ['limit' => self::LIMIT];

        if (!empty($activeFilters['price_min'])) {
            $mcpOptions['price_min'] = (float) $activeFilters['price_min'];
        }
        if (!empty($activeFilters['price_max'])) {
            $mcpOptions['price_max'] = (float) $activeFilters['price_max'];
        }
        if (!empty($activeFilters['sort'])) {
            $mcpOptions['sort'] = $activeFilters['sort'];
        }
        // required_features передаём только если их явно задал пользователь/интерфейс (жёсткий фильтр).
        if (!empty($userFilters['required_features'])) {
            $mcpOptions['required_features'] = $userFilters['required_features'];
        }

        // Цепочка: основной запрос -> запрос на другом языке -> упрощённый. Идём дальше только при пустой выдаче.
        // SEARCH_MAX_QUERIES (по умолчанию 1): сколько успешных запросов объединять. 1 = только 8 лучших,
        // 3 = до 24 товаров, но дополнительные запросы (упрощённый, другой язык) менее точны.
        $maxQueries = max(1, min(3, (int) config('services.bestprice.max_queries', 1)));

        $collected = [];
        $usedAttempt = null;
        $brandMismatch = false;
        $candidate = null;
        $okQueries = 0;
        $tried = 0;
        $failed = 0;
        $limited = false;
        $brand = trim((string) ($analysis['brand'] ?? ''));

        foreach ($this->buildAttempts($analysis) as $attempt) {
            $tried++;
            $response = $this->bestPrice->searchProducts($attempt['query'], $mcpOptions);

            if (!$response['ok']) {
                $failed++;

                // BestPrice ограничил частоту запросов: дальнейшие попытки бесполезны.
                if (!empty($response['rate_limited'])) {
                    $limited = true;
                    break;
                }

                continue;
            }

            if (empty($response['products'])) {
                continue;
            }

            // Бренд запрошен, но ни в одном названии его нет: выдача нерелевантна, пробуем следующий запрос.
            if ($brand !== '' && !$this->anyTitleHasBrand($response['products'], $brand)) {
                $candidate ??= ['products' => $response['products'], 'attempt' => $attempt];
                continue;
            }

            $collected = array_merge($collected, $response['products']);
            $usedAttempt ??= $attempt;
            $okQueries++;

            if ($okQueries >= $maxQueries) {
                break;
            }
        }

        $rawProducts = $collected;

        // Ни один запрос не дал товаров нужного бренда: показываем первую непустую выдачу, но помечаем это.
        if (empty($rawProducts) && $candidate !== null) {
            $rawProducts = $candidate['products'];
            $usedAttempt = $candidate['attempt'];
            $brandMismatch = true;
        }

        if (empty($rawProducts)) {
            // Все попытки упали с ошибкой: это сбой сервиса, а не «ничего не найдено».
            if ($tried > 0 && $failed === $tried) {
                return $this->emptyResult(
                    $userQuery,
                    $activeFilters,
                    $limited
                        ? __('Каталог BestPrice сейчас перегружен запросами. Повторите поиск через минуту.')
                        : __('Каталог BestPrice временно недоступен. Попробуйте позже.'),
                    ['_no_cache' => true, 'intent_analysis' => $analysis]
                );
            }

            return $this->emptyResult(
                $userQuery,
                $activeFilters,
                __('По вашему запросу ничего не найдено в каталоге BestPrice. Попробуйте уточнить параметры.'),
                ['intent_analysis' => $analysis]
            );
        }

        $rawProducts = $this->dedupe($rawProducts);

        $titlesToTranslate = array_map(fn ($item) => $item['title'] ?? 'Product', $rawProducts);
        $translatedTitles = $this->groqService->translateProductsBack($titlesToTranslate, $targetLang);

        $products = [];

        foreach ($rawProducts as $index => $item) {
            $priceAmount = isset($item['price_from']) && is_numeric($item['price_from'])
                ? (float) $item['price_from']
                : (isset($item['price']) && is_numeric($item['price']) ? (float) $item['price'] : null);

            $displayTitle = $translatedTitles[$index] ?? ($item['title'] ?? 'Товар');

            $formattedPrice = ($priceAmount !== null && $priceAmount > 0)
                ? '€' . number_format($priceAmount, 2, '.', ' ')
                : __('По запросу');

            $rating = $item['rating'] ?? null;
            $rating = is_array($rating) ? ($rating['average'] ?? null) : (is_numeric($rating) ? $rating : null);

            $products[] = [
                'title' => $displayTitle,
                'original_title' => $item['title'] ?? '',
                'merchant' => $item['brand'] ?? $item['merchant_name'] ?? 'BestPrice Store',
                'merchant_name' => $item['brand'] ?? $item['merchant_name'] ?? 'BestPrice Store',

                // Дублируем возможные варианты ключей цены для совместимости с шаблонами
                'price' => $priceAmount,
                'price_from' => $priceAmount,
                'price_amount' => $priceAmount,
                'price_formatted' => $formattedPrice,
                'price_note' => $formattedPrice,
                'formatted_price' => $formattedPrice,

                'currency' => 'EUR',
                'offers_count' => $item['offer_count'] ?? $item['offers_count'] ?? 1,
                'rating' => $rating,
                'image_url' => filter_var($item['image_url'] ?? '', FILTER_VALIDATE_URL) ?: null,
                'product_url' => filter_var($item['bestprice_url'] ?? $item['product_url'] ?? '', FILTER_VALIDATE_URL) ?: '#',
            ];
        }

        return [
            'query_summary' => $userQuery,
            'recommendation' => __('Найдено уникальных предложений в магазинах Греции: ') . count($products),
            'products' => $products,
            'active_filters' => $activeFilters,
            'intent_analysis' => $analysis,
            'search_meta' => [
                'query_used' => $usedAttempt['query'] ?? null,
                'language_used' => $usedAttempt['lang'] ?? null,
                'fallback_used' => ($usedAttempt['step'] ?? 0) > 0,
                'brand_mismatch' => $brandMismatch,
                'queries_merged' => $okQueries,
            ],
        ];
    }

    /**
     * Порядок попыток: язык, который выбрала LLM -> второй язык -> упрощённый греческий. Без дублей.
     */
    protected function buildAttempts(array $analysis): array
    {
        $el = trim((string) ($analysis['query_el'] ?? $analysis['greek_query'] ?? ''));
        $en = trim((string) ($analysis['query_en'] ?? ''));
        $relaxed = trim((string) ($analysis['relaxed_el'] ?? ''));
        $preferEn = ($analysis['preferred_language'] ?? 'el') === 'en';

        $ordered = $preferEn
            ? [['query' => $en, 'lang' => 'en'], ['query' => $el, 'lang' => 'el']]
            : [['query' => $el, 'lang' => 'el'], ['query' => $en, 'lang' => 'en']];

        $ordered[] = ['query' => $relaxed, 'lang' => 'el'];

        $attempts = [];
        $seen = [];

        foreach ($ordered as $candidate) {
            $key = mb_strtolower($candidate['query']);

            if ($candidate['query'] === '' || isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $candidate['step'] = count($attempts);
            $attempts[] = $candidate;
        }

        return $attempts;
    }

    /**
     * Язык ответа: по алфавиту запроса; для латиницы берём язык интерфейса.
     */
    protected function resolveTargetLang(string $userQuery, string $locale): string
    {
        $detected = GroqAiService::detectLanguage($userQuery);

        if (in_array($detected, ['ru', 'ka', 'el'], true)) {
            return $detected;
        }

        return in_array($locale, ['ru', 'ka', 'en', 'el'], true) ? $locale : 'en';
    }

    /**
     * Принимает оба варианта имён (min_price / price_min) и отбрасывает пустые значения,
     * чтобы они не затирали фильтры, найденные ИИ.
     */
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
        // 'relevance' из контроллера означает «пользователь ничего не выбирал»: не затираем сортировку ИИ
        // («самые дешёвые» -> price_asc).
        if (!empty($filters['sort']) && in_array($filters['sort'], ['price_asc', 'price_desc'], true)) {
            $out['sort'] = $filters['sort'];
        }
        if (!empty($filters['required_features']) && is_array($filters['required_features'])) {
            $out['required_features'] = array_values($filters['required_features']);
        }
        if (!empty($filters['skip_clarification'])) {
            $out['skip_clarification'] = true;
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

    /**
     * Журнал запросов storage/logs/search-YYYY-MM-DD.log (30 дней): по нему видно, какие запросы идут в LLM
     * (route=llm), и что стоит добавить в config/search_glossary.php. route: local | llm | llm_cache | result_cache | degraded.
     */
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
                'route' => $route ?? ($analysis['source'] ?? 'unknown'),
                'model' => $analysis['model'] ?? null,
                'products' => count($result['products'] ?? []),
                'clarify' => !empty($result['needs_clarification']),
                'query_used' => $result['search_meta']['query_used'] ?? null,
                'fallback' => $result['search_meta']['fallback_used'] ?? null,
                'brand_mismatch' => $result['search_meta']['brand_mismatch'] ?? null,
                'ms' => (int) round((microtime(true) - $startedAt) * 1000),
            ]);
        } catch (\Throwable $e) {
            // журнал не должен ломать поиск
        }
    }

    protected function anyTitleHasBrand(array $products, string $brand): bool
    {
        foreach ($products as $item) {
            if (mb_stripos((string) ($item['title'] ?? ''), $brand) !== false) {
                return true;
            }
        }

        return false;
    }

    protected function dedupe(array $products): array
    {
        $seen = [];
        $unique = [];

        foreach ($products as $item) {
            $key = mb_strtolower((string) ($item['bestprice_url'] ?? $item['product_url'] ?? $item['title'] ?? uniqid('', true)));

            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $unique[] = $item;
        }

        return $unique;
    }

    protected function emptyResult(string $userQuery, array $activeFilters, string $message, array $extra = []): array
    {
        return array_merge([
            'query_summary' => $userQuery,
            'recommendation' => $message,
            'products' => [],
            'active_filters' => $activeFilters,
        ], $extra);
    }
}
