<?php

namespace App\Services;

class ProductSearchPlanner
{
    protected BestPriceMcpService $mcpService;

    public function __construct(BestPriceMcpService $mcpService)
    {
        $this->mcpService = $mcpService;
    }

    /**
     * Выполняет многошаговый сбор товаров с обходом лимита в 8 результатов за вызов,
     * объединяет выдачи и удаляет дубликаты.
     */
    public function planAndExecuteSearch(string $primaryGreekQuery, array $alternativeQueries = [], array $options = []): array
    {
        $allProducts = [];
        $seenIds = [];

        // 1. Формируем пул уникальных поисковых запросов (начинаем с основного)
        $queriesToRun = array_unique(array_merge([$primaryGreekQuery], $alternativeQueries));
        
        // Ограничиваем максимальное количество запросов за один сеанс (защита от перегрузки MCP)
        $queriesToRun = array_slice($queriesToRun, 0, 3);

        foreach ($queriesToRun as $index => $query) {
            // Для дополнительных запросов можем мягко сбрасывать жесткие фильтры характеристик, 
            // чтобы расширить охват, если первый запрос дал мало результатов
            $currentOptions = $options;
            if ($index > 0) {
                unset($currentOptions['required_features']);
            }

            $results = $this->mcpService->searchProducts($query, $currentOptions);

            if (empty($results)) {
                continue;
            }

            // 2. Объединяем результаты и удаляем дубликаты
            foreach ($results as $item) {
                // Определяем уникальный идентификатор товара (по ID от BestPrice или каноническому URL)
                $uniqueId = $item['id'] ?? $item['bestprice_url'] ?? $item['product_url'] ?? md5($item['title'] ?? '');

                if (!empty($uniqueId) && !isset($seenIds[$uniqueId])) {
                    $seenIds[$uniqueId] = true;
                    $allProducts[] = $item;
                }
            }

            // Если уже собрали достаточное количество уникальных товаров, прекращаем циклы
            if (count($allProducts) >= 24) {
                break;
            }
        }

        return array_values($allProducts);
    }
}