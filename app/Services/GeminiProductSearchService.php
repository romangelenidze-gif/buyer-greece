<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GeminiProductSearchService
{
    protected string $serpApiKey;
    protected string $groqApiKey;

    public function __construct()
    {
        $this->serpApiKey = config('services.serpapi.key', env('SERPAPI_API_KEY', ''));
        $this->groqApiKey = config('services.groq.api_key', env('GROQ_API_KEY', ''));
    }

    public function search(string $query): array
    {
        // Жесткая нормализация запроса для идеального кэширования
        $cleanQuery = mb_strtolower(trim(preg_replace('/[^\p{L}\p{N}\s]+/u', '', $query)));
        $cleanQuery = preg_replace('/\s+/', ' ', $cleanQuery);
        
        $cacheKey = 'greek_search_text_v3_' . md5($cleanQuery);
        $ttl = 604800; // 7 дней кэша

        // Принудительно используем file-store, чтобы кэш гарантированно работал независимо от .env
        return Cache::store('file')->remember($cacheKey, $ttl, function () use ($query) {
            return $this->fetchTextProducts($query);
        });
    }

    protected function fetchTextProducts(string $query): array
    {
        if (empty($this->serpApiKey)) {
            Log::error('SerpApi API Key is missing.');
            return $this->errorResponse($query, 'Сервис поиска не настроен (отсутствует SERPAPI_API_KEY).');
        }

        $greekQuery = $this->optimizeQueryForGreekMarket($query);

        try {
            $response = Http::timeout(25)
                ->withoutVerifying()
                ->get('https://serpapi.com/search.json', [
                    'engine' => 'google',
                    'q' => $greekQuery,
                    'google_domain' => 'google.gr',
                    'gl' => 'gr',
                    'hl' => 'en',
                    'location' => 'Athens, Greece',
                    'api_key' => $this->serpApiKey,
                ]);

            if ($response->failed()) {
                Log::error('SerpApi request failed: ' . $response->body());
                return $this->errorResponse($query, 'Не удалось получить данные из поисковой системы.');
            }

            $data = $response->json();
            $products = [];

            // 1. Товарные блоки Google (Inline Shopping)
            if (!empty($data['inline_shopping_results'])) {
                foreach ($data['inline_shopping_results'] as $item) {
                    if (count($products) >= 6) break;

                    $link = $item['link'] ?? $item['product_link'] ?? '';
                    if (!$link || !filter_var($link, FILTER_VALIDATE_URL)) continue;

                    $title = strip_tags($item['title'] ?? 'Товар');
                    $merchant = strip_tags($item['source'] ?? 'Магазин');

                    $priceAmount = isset($item['extracted_price']) ? (float)$item['extracted_price'] : null;
                    $priceNote = $item['price'] ?? '';
                    if (!$priceAmount && $priceNote) {
                        $cleanPrice = preg_replace('/[^\d.,]/', '', str_replace(',', '.', $priceNote));
                        $priceAmount = $cleanPrice ? (float)$cleanPrice : null;
                    }

                    $products[] = [
                        'title' => $title,
                        'merchant_name' => $merchant,
                        'price_amount' => $priceAmount,
                        'currency' => $item['currency'] ?? 'EUR',
                        'price_note' => $priceNote ?: ($priceAmount ? '€' . number_format($priceAmount, 2) : ''),
                        'product_url' => $link,
                        'short_description' => strip_tags($item['snippet'] ?? $title),
                    ];
                }
            }

            // 2. Органическая выдача (Organic Results)
            if (!empty($data['organic_results'])) {
                foreach ($data['organic_results'] as $item) {
                    if (count($products) >= 6) break;

                    $link = $item['link'] ?? '';
                    if (!$link || !filter_var($link, FILTER_VALIDATE_URL)) continue;

                    $host = parse_url($link, PHP_URL_HOST) ?? '';
                    if (str_contains($host, 'google.') || str_contains($host, 'facebook.') || str_contains($host, 'youtube.') || str_contains($host, 'wikipedia.')) {
                        continue;
                    }

                    if (collect($products)->contains('product_url', $link)) {
                        continue;
                    }

                    $title = strip_tags($item['title'] ?? 'Товар');
                    $snippet = strip_tags($item['snippet'] ?? '');
                    $merchant = preg_replace('/^www\./i', '', $host);

                    $priceAmount = null;
                    $priceNote = '';
                    if (isset($item['rich_snippet']['top']['detected_extensions']['price'])) {
                        $priceAmount = (float)$item['rich_snippet']['top']['detected_extensions']['price'];
                        $priceNote = '€' . number_format($priceAmount, 2);
                    } elseif (preg_match('/(\d+(?:[.,]\d+)?)\s*(?:€|EUR)/ui', $snippet, $matches)) {
                        $priceAmount = (float)str_replace(',', '.', $matches[1]);
                        $priceNote = '€' . number_format($priceAmount, 2);
                    }

                    $products[] = [
                        'title' => $title,
                        'merchant_name' => strtoupper($merchant),
                        'price_amount' => $priceAmount,
                        'currency' => 'EUR',
                        'price_note' => $priceNote,
                        'product_url' => $link,
                        'short_description' => $snippet,
                    ];
                }
            }

            if (empty($products)) {
                return [
                    'query_summary' => $query,
                    'recommendation' => 'По вашему запросу ничего не найдено в греческих магазинах.',
                    'products' => []
                ];
            }

            return [
                'query_summary' => $query,
                'recommendation' => 'Найдены актуальные предложения в греческих интернет-магазинах с прямыми ссылками и ценами.',
                'products' => $products,
            ];

        } catch (\Exception $e) {
            Log::error('Search exception: ' . $e->getMessage());
            return $this->errorResponse($query, 'Произошла ошибка при выполнении поиска.');
        }
    }

    protected function optimizeQueryForGreekMarket(string $query): string
    {
        if (empty($this->groqApiKey)) {
            return $query;
        }

        try {
            $response = Http::timeout(10)
                ->withToken($this->groqApiKey)
                ->withoutVerifying()
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'openai/gpt-oss-120b',
                    'messages' => [
                        [
                            'role' => 'system', 
                            'content' => 'You are an e-commerce product search assistant for Greek market (Skroutz, Public, IKEA Greece, etc.). ' .
                                         'Translate the user search query into exact Greek product query keywords used in Greek e-commerce. ' .
                                         'Return ONLY the Greek translation phrase, no punctuation, no explanation.'
                        ],
                        ['role' => 'user', 'content' => $query]
                    ]
                ]);

            if ($response->successful()) {
                $content = trim($response->json('choices.0.message.content', ''));
                if (!empty($content)) {
                    return trim($content, '"\'');
                }
            }
        } catch (\Exception $e) {
            Log::error('Query translation error: ' . $e->getMessage());
        }

        return $query;
    }

    protected function errorResponse(string $query, string $message): array
    {
        return [
            'query_summary' => $query,
            'recommendation' => $message,
            'products' => []
        ];
    }
}