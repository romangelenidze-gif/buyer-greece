<?php

namespace App\Services;

class SmartFacetService
{
    /**
     * Формирует динамические фасеты и счетчики на основе ФАКТИЧЕСКИ найденных товаров.
     */
    public function generateFacets(array $products): array
    {
        if (empty($products)) {
            return [];
        }

        $brands = [];
        $prices = [];
        $merchants = [];

        foreach ($products as $product) {
            // 1. Подсчет брендов
            $brand = $product['merchant_name'] ?? $product['brand'] ?? null;
            if ($brand && $brand !== 'BestPrice Store' && $brand !== 'BestPrice') {
                $brands[$brand] = ($brands[$brand] ?? 0) + 1;
            }

            // 2. Сбор цен для определения минимального и максимального диапазона
            if (!empty($product['price_amount'])) {
                $prices[] = (float)$product['price_amount'];
            }
        }

        arsort($brands);

        $minPrice = !empty($prices) ? floor(min($prices)) : 0;
        $maxPrice = !empty($prices) ? ceil(max($prices)) : 0;

        return [
            'brands' => array_slice($brands, 0, 8, true), // Топ-8 брендов с количеством предложений
            'price_range' => [
                'min' => $minPrice,
                'max' => $maxPrice,
            ],
            'total_found' => count($products),
        ];
    }
}