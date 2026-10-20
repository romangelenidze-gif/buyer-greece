<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BestPriceMcpService
{
    protected string $mcpEndpoint = 'https://mcp.bestprice.gr/mcp';

    /**
     * Поиск товаров через BestPrice MCP с поддержкой официальных параметров фильтрации и лимита до 8 результатов.
     */
    public function searchProducts(string $greekQuery, array $options = []): array
    {
        Log::info('BestPrice MCP: Starting search request', [
            'query' => $greekQuery,
            'options' => $options
        ]);

        try {
            // 1. Инициализация сессии MCP по спецификации JSON-RPC
            $initPayload = [
                'jsonrpc' => '2.0',
                'method' => 'initialize',
                'params' => [
                    'protocolVersion' => '2024-11-05',
                    'capabilities' => [],
                    'clientInfo' => [
                        'name' => 'BuyerGreeceLaravelClient',
                        'version' => '1.0.0'
                    ]
                ],
                'id' => 1,
            ];

            // Используем штатную проверку TLS согласно требованиям безопасности
            Http::timeout(10)->post($this->mcpEndpoint, $initPayload);

            // 2. Формируем аргументы для инструмента search_products согласно ТЗ
            $arguments = [
                'query' => $greekQuery,
            ];

            if (!empty($options['price_min'])) {
                $arguments['price_min'] = (float)$options['price_min'];
            }

            if (!empty($options['price_max'])) {
                $arguments['price_max'] = (float)$options['price_max'];
            }

            // Допустимые значения сортировки по API BestPrice: relevance, price_asc, price_desc
            if (!empty($options['sort']) && in_array($options['sort'], ['relevance', 'price_asc', 'price_desc'])) {
                $arguments['sort'] = $options['sort'];
            }

            if (!empty($options['required_features']) && is_array($options['required_features'])) {
                $arguments['required_features'] = $options['required_features'];
            }

            // Жесткое ограничение MCP: не более 8 результатов за один вызов
            $arguments['limit'] = min(8, (int)($options['limit'] ?? 8));

            $payload = [
                'jsonrpc' => '2.0',
                'method' => 'tools/call',
                'params' => [
                    'name' => 'search_products',
                    'arguments' => $arguments
                ],
                'id' => 2,
            ];

            $response = Http::timeout(20)->post($this->mcpEndpoint, $payload);

            // Добавьте этот блок для отладки
Log::info('BestPrice MCP RAW JSON Response:', [
    'status' => $response->status(),
    'body' => $response->json()
]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Основной надежный источник: structuredContent->products
                if (isset($data['result']['structuredContent']['products']) && is_array($data['result']['structuredContent']['products'])) {
                    return $data['result']['structuredContent']['products'];
                }

                // Запасной вариант через content текст
                if (isset($data['result']['content'])) {
                    foreach ($data['result']['content'] as $contentBlock) {
                        if (isset($contentBlock['text'])) {
                            if (preg_match('/Structured result \(JSON\):\s*(\{.*\})/s', $contentBlock['text'], $matches)) {
                                $jsonDecoded = json_decode($matches[1], true);
                                if (isset($jsonDecoded['products']) && is_array($jsonDecoded['products'])) {
                                    return $jsonDecoded['products'];
                                }
                            }
                        }
                    }
                }
            } else {
                Log::error('BestPrice MCP HTTP error: ' . $response->status(), [
                    'body' => $response->body()
                ]);
            }

        } catch (\Exception $e) {
            Log::error('BestPrice MCP exception: ' . $e->getMessage());
        }

        return [];
    }
}