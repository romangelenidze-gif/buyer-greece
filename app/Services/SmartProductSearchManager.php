<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SmartProductSearchManager
{
    protected GroqAiService $groqService;
    protected ProductSearchPlanner $searchPlanner;

    public function __construct(GroqAiService $groqService, ProductSearchPlanner $searchPlanner)
    {
        $this->groqService = $groqService;
        $this->searchPlanner = $searchPlanner;
    }

    public function search(string $userQuery, string $locale = 'ru', array $userFilters = []): array
    {
        $normalizedQuery = mb_strtolower(trim(preg_replace('/[^\p{L}\p{N}\s]+/u', '', $userQuery)));
        
        $targetLang = 'el';
        if (preg_match('/[\x{0400}-\x{04FF}]/u', $userQuery)) {
            $targetLang = 'ru';
        } elseif (preg_match('/[\x{10A0}-\x{10FF}]/u', $userQuery)) {
            $targetLang = 'ka';
        }

        $cacheKey = 'buyer_greece_search_v8_' . md5($normalizedQuery . '_' . $targetLang . '_' . json_encode($userFilters));

        return Cache::store('file')->remember($cacheKey, 10800, function () use ($userQuery, $targetLang, $userFilters) {
            
            // 1. Получаем глубокий анализ от Groq (греческий запрос, альтернативы, фильтры)
            $analysis = $this->groqService->analyzeQuery($userQuery);
            $greekQuery = $analysis['greek_query'] ?? $userQuery;
            $alternativeQueries = $analysis['alternative_queries'] ?? [];
            $aiFilters = $analysis['filters'] ?? [];

            $activeFilters = array_merge($aiFilters, $userFilters);

            $mcpOptions = [
                'limit' => 8,
            ];

            if (!empty($activeFilters['min_price'])) {
                $mcpOptions['price_min'] = (float)$activeFilters['min_price'];
            }
            if (!empty($activeFilters['max_price'])) {
                $mcpOptions['price_max'] = (float)$activeFilters['max_price'];
            }
            if (!empty($activeFilters['sort'])) {
                $mcpOptions['sort'] = $activeFilters['sort'];
            }
            if (!empty($activeFilters['required_features'])) {
                $mcpOptions['required_features'] = (array)$activeFilters['required_features'];
            }

            // 2. Запускаем планировщик многошагового поиска через MCP
            $rawProducts = $this->searchPlanner->planAndExecuteSearch($greekQuery, $alternativeQueries, $mcpOptions);

            if (empty($rawProducts)) {
                return [
                    'query_summary' => $userQuery,
                    'recommendation' => __('По вашему запросу ничего не найдено в каталоге BestPrice. Попробуйте уточнить параметры.'),
                    'products' => [],
                    'active_filters' => $activeFilters,
                ];
            }

            // 3. Пакетный перевод названий обратно на язык пользователя
            $titlesToTranslate = array_map(fn($item) => $item['title'] ?? 'Product', $rawProducts);
            $translatedTitles = $this->groqService->translateProductsBack($titlesToTranslate, $targetLang);

            $products = [];
            foreach ($rawProducts as $index => $item) {
                $priceAmount = isset($item['price_from']) ? (float)$item['price_from'] : (isset($item['price']) ? (float)$item['price'] : null);
                $displayTitle = $translatedTitles[$index] ?? ($item['title'] ?? 'Товар');

                $estimatedWeightKg = $this->estimateProductWeight($displayTitle);
                $estimatedCamexShippingGEL = round($estimatedWeightKg * 9.0, 2);

                $products[] = [
                    'title' => $displayTitle,
                    'original_title' => $item['title'] ?? '',
                    'merchant_name' => $item['brand'] ?? $item['merchant_name'] ?? 'BestPrice Store',
                    'price_amount' => $priceAmount,
                    'currency' => 'EUR',
                    'price_note' => $priceAmount ? '€' . number_format($priceAmount, 2) : '',
                    'offers_count' => $item['offer_count'] ?? $item['offers_count'] ?? 1,
                    'availability' => $item['availability'] ?? null,
                    'image_url' => filter_var($item['image_url'] ?? '', FILTER_VALIDATE_URL) ?: null,
                    'product_url' => filter_var($item['bestprice_url'] ?? $item['product_url'] ?? '', FILTER_VALIDATE_URL) ?: '#',
                    'short_description' => $item['category_title'] ?? ('Предложений магазинов: ' . ($item['offer_count'] ?? 1)),
                    'shipping_estimate' => "~{$estimatedWeightKg} кг (~{$estimatedCamexShippingGEL} GEL до Тбилиси)",
                    'match_reason' => __('Проверено каталогом BestPrice.gr'),
                ];
            }

            return [
                'query_summary' => $userQuery,
                'recommendation' => __('Найдено уникальных предложений в магазинах Греции: ') . count($products),
                'products' => $products,
                'active_filters' => $activeFilters,
                'intent_analysis' => $analysis,
            ];
        });
    }

    protected function estimateProductWeight(string $title): float
    {
        $t = mb_strtolower($title);
        if (str_contains($t, 'μπουφάν') || str_contains($t, 'jacket') || str_contains($t, 'пальто')) return 1.2;
        if (str_contains($t, 'λάδι') || str_contains($t, '5l') || str_contains($t, 'oil')) return 4.8;
        if (str_contains($t, 'παπούτσια') || str_contains($t, 'shoes') || str_contains($t, 'кроссовки')) return 0.9;
        return 0.6;
    }
}