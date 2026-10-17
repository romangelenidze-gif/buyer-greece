<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BestPriceMcpService
{
    protected string $mcpEndpoint = 'https://mcp.bestprice.gr/mcp';

    public function searchProducts(string $greekQuery): array
    {
        Log::info('BestPrice MCP: Starting search request', [
            'query' => $greekQuery
        ]);

        try {
            // 1. Инициализация сессии MCP
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

            Http::timeout(10)->withoutVerifying()->post($this->mcpEndpoint, $initPayload);

            // 2. Вызов инструмента поиска товаров
            $payload = [
                'jsonrpc' => '2.0',
                'method' => 'tools/call',
                'params' => [
                    'name' => 'search_products',
                    'arguments' => [
                        'query' => $greekQuery,
                    ]
                ],
                'id' => 2,
            ];

            $response = Http::timeout(20)
                ->withoutVerifying()
                ->post($this->mcpEndpoint, $payload);

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
                            // Пытаемся найти JSON внутри текста, если structuredContent отсутствует
                            if (preg_match('/Structured result \(JSON\):\s*(\{.*\})/s', $contentBlock['text'], $matches)) {
                                $jsonDecoded = json_decode($matches[1], true);
                                if (isset($jsonDecoded['products']) && is_array($jsonDecoded['products'])) {
                                    return $jsonDecoded['products'];
                                }
                            }
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            Log::error('BestPrice MCP exception: ' . $e->getMessage());
        }

        return [];
    }
}