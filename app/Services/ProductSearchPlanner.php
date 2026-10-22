<?php

namespace App\Services;

class ProductSearchPlanner
{
    protected BestPriceMcpService $mcpService;

    public function __construct(BestPriceMcpService $mcpService)
    {
        $this->mcpService = $mcpService;
    }

    public function planAndExecuteSearch(string $primaryGreekQuery, array $alternativeQueries = [], array $options = []): array
    {
        $allProducts = [];
        $seenIds = [];

        $queriesToRun = array_unique(array_merge([$primaryGreekQuery], $alternativeQueries));
        $queriesToRun = array_slice($queriesToRun, 0, 3);

        foreach ($queriesToRun as $index => $query) {
            $currentOptions = $options;
            if ($index > 0) {
                unset($currentOptions['required_features']);
            }

            $results = $this->mcpService->searchProducts($query, $currentOptions);

            if (empty($results)) {
                continue;
            }

            foreach ($results as $item) {
                $uniqueId = $item['id'] ?? $item['bestprice_url'] ?? $item['product_url'] ?? md5($item['title'] ?? '');

                if (!empty($uniqueId) && !isset($seenIds[$uniqueId])) {
                    $seenIds[$uniqueId] = true;
                    $allProducts[] = $item;
                }
            }

            if (count($allProducts) >= 24) {
                break;
            }
        }

        return array_values($allProducts);
    }
}