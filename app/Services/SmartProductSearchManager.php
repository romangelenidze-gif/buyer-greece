<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SmartProductSearchManager
{
    protected GroqAiService $groqService;
    protected BestPriceMcpService $mcpService;

    public function __construct(GroqAiService $groqService, BestPriceMcpService $mcpService)
    {
        $this->groqService = $groqService;
        $this->mcpService = $mcpService;
    }

    public function search(string $userQuery, string $locale = 'ru'): array
    {
        $normalizedQuery = mb_strtolower(trim(preg_replace('/[^\p{L}\p{N}\s]+/u', '', $userQuery)));
        $normalizedQuery = preg_replace('/\s+/', ' ', $normalizedQuery);

        // Определяем язык исходного запроса для обратного перевода
        $targetLang = 'el';
        if (preg_match('/[\x{0400}-\x{04FF}]/u', $userQuery)) {
            $targetLang = 'ru';
        } elseif (preg_match('/[\x{10A0}-\x{10FF}]/u', $userQuery)) {
            $targetLang = 'ka';
        }

        $cacheKey = 'buyer_greece_search_v5_' . md5($normalizedQuery . '_' . $targetLang);

        return Cache::store('file')->remember($cacheKey, 10800, function () use ($userQuery, $targetLang) {
            
            $analysis = $this->groqService->analyzeQuery($userQuery);
            $greekQuery = $analysis['greek_query'] ?? $userQuery;

            $rawProducts = $this->mcpService->searchProducts($greekQuery);

            if (empty($rawProducts)) {
                return [
                    'query_summary' => $userQuery,
                    'recommendation' => 'По вашему запросу ничего не найдено в каталоге BestPrice.',
                    'products' => [],
                ];
            }

            // Собираем массив оригинальных греческих названий для пакетного перевода
            $limitedRawProducts = array_slice($rawProducts, 0, 12);
            $titlesToTranslate = array_map(fn($item) => $item['title'] ?? 'Product', $limitedRawProducts);

            // Переводим названия обратно на язык пользователя (сохраняя бренды)
            $translatedTitles = $this->groqService->translateProductsBack($titlesToTranslate, $targetLang);

            $products = [];
            foreach ($limitedRawProducts as $index => $item) {
                $priceAmount = isset($item['price_from']) ? (float)$item['price_from'] : (isset($item['price']) ? (float)$item['price'] : null);
                
                // Берем переведенное название или оригинал как фоллбек
                $displayTitle = $translatedTitles[$index] ?? ($item['title'] ?? 'Товар');

                $products[] = [
                    'title' => $displayTitle,
                    'original_title' => $item['title'] ?? '',
                    'merchant_name' => $item['brand'] ?? 'BestPrice',
                    'price_amount' => $priceAmount,
                    'currency' => 'EUR',
                    'price_note' => $priceAmount ? '€' . number_format($priceAmount, 2) : '',
                    'offers_count' => $item['offer_count'] ?? $item['offers_count'] ?? 1,
                    'availability' => $item['availability'] ?? null,
                    'image_url' => filter_var($item['image_url'] ?? '', FILTER_VALIDATE_URL) ?: null,
                    'product_url' => filter_var($item['bestprice_url'] ?? $item['product_url'] ?? '', FILTER_VALIDATE_URL) ?: '#',
                    'short_description' => $item['category_title'] ?? ('Предложений: ' . ($item['offer_count'] ?? 1)),
                    'match_reason' => 'Подтверждено каталогом BestPrice.gr',
                ];
            }

            return [
                'query_summary' => $userQuery,
                'recommendation' => 'Найдены актуальные предложения в греческих интернет-магазинах через BestPrice MCP.',
                'products' => $products,
            ];
        });
    }
}