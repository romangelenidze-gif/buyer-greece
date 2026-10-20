<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqAiService
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key', env('GROQ_API_KEY', ''));
        $this->model = 'openai/gpt-oss-20b';
    }

    /**
     * Анализирует запрос пользователя, исправляет опечатки, извлекает интенты и фильтры,
     * а также формирует естественный поисковый запрос на греческом языке для BestPrice.gr.
     */
    public function analyzeQuery(string $userQuery): array
    {
        $cleanQuery = trim($userQuery);

        if (empty($cleanQuery) || empty($this->apiKey)) {
            return $this->fallbackAnalysis($cleanQuery);
        }

        // Определяем язык исходного запроса
        $sourceLang = 'en';
        if (preg_match('/[\x{0400}-\x{04FF}]/u', $cleanQuery)) {
            $sourceLang = 'ru';
        } elseif (preg_match('/[\x{10A0}-\x{10FF}]/u', $cleanQuery)) {
            $sourceLang = 'ka';
        } elseif (preg_match('/[\x{0370}-\x{03FF}]/u', $cleanQuery)) {
            $sourceLang = 'el';
        }

        $systemPrompt = <<<PROMPT
You are an elite e-commerce AI search assistant and query parser for BestPrice.gr (Greek marketplace).
Analyze the user query (which may be in Russian, Georgian, English, or Greek, and may contain typos) and extract structured search parameters.

Strict Rules:
1. "greek_query": Correct typos, translate descriptive terms into natural commercial Greek search keywords. KEEP BRAND NAMES (e.g. Nike, Adidas, Sony, Apple) in Latin script. Do NOT include price terms in "greek_query".
2. "source_language": Identified language ("ru", "ka", "en", or "el").
3. "intent": One of "product_search", "price_check", or "recommendation".
4. "brand": Brand name in Latin if present, otherwise null.
5. "filters": Object containing:
   - "price_min": float number or null
   - "price_max": float number or null
   - "sort": Allowed string ONLY: "relevance", "price_asc", or "price_desc" (null if not specified)
   - "required_features": Array of key terms/attributes in Greek (e.g. ["γυναικεία", "τζιν"]) or empty array []
6. Return ONLY a valid JSON object matching this schema. No markdown formatting, no text before or after JSON.
PROMPT;

        try {
            // Включаем штатную проверку TLS согласно требованиям безопасности
            $response = Http::timeout(15)
                ->withToken($this->apiKey)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $cleanQuery],
                    ],
                    'response_format' => ['type' => 'json_object'],
                    'temperature' => 0.0,
                ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content', '{}');
                $content = preg_replace('/<think>[\s\S]*?<\/think>/u', '', $content);
                $content = preg_replace('/^```(?:json)?\s*([\s\S]*?)\s*```$/u', '$1', trim($content));

                $decoded = json_decode($content, true);

                if (is_array($decoded) && !empty($decoded['greek_query'])) {
                    // Валидация значений сортировки для BestPrice API
                    $sort = $decoded['filters']['sort'] ?? 'relevance';
                    if (!in_array($sort, ['relevance', 'price_asc', 'price_desc'])) {
                        $sort = 'relevance';
                    }

                    return [
                        'intent' => $decoded['intent'] ?? 'product_search',
                        'source_language' => $decoded['source_language'] ?? $sourceLang,
                        'greek_query' => trim($decoded['greek_query']),
                        'brand' => $decoded['brand'] ?? null,
                        'filters' => [
                            'price_min' => isset($decoded['filters']['price_min']) ? (float)$decoded['filters']['price_min'] : null,
                            'price_max' => isset($decoded['filters']['price_max']) ? (float)$decoded['filters']['price_max'] : null,
                            'sort' => $sort,
                            'required_features' => is_array($decoded['filters']['required_features'] ?? null) ? $decoded['filters']['required_features'] : [],
                        ],
                    ];
                }
            } else {
                Log::error('Groq API error HTTP ' . $response->status(), ['body' => $response->body()]);
            }
        } catch (\Exception $e) {
            Log::error('Groq API Exception in analyzeQuery: ' . $e->getMessage());
        }

        return $this->fallbackAnalysis($cleanQuery, $sourceLang);
    }

    /**
     * Пакетный перевод названий товаров обратно на язык пользователя (RU/KA).
     * Сохраняет бренды, артикулы, модели, единицы измерения в исходной латинице.
     */
    public function translateProductsBack(array $titles, string $targetLanguage): array
    {
        if (empty($titles) || in_array($targetLanguage, ['el', 'en'])) {
            return $titles;
        }

        if (empty($this->apiKey)) {
            return $titles;
        }

        $langName = $targetLanguage === 'ru' ? 'Russian' : ($targetLanguage === 'ka' ? 'Georgian' : 'English');

        $systemPrompt = <<<PROMPT
You are a professional e-commerce translator. Translate the given array of Greek product titles into {$langName}.

Strict Rules:
1. KEEP ALL BRAND NAMES (e.g. Adidas, Nike, Apple, Guess, Samsung, Sony), model codes, serial numbers, dimensions, and technical units (ml, L, kg, GB, cm, mm) strictly in Latin characters/numbers as in the original text.
2. Translate only descriptive general words (like jacket, dress, shoes, waterproof, black, women's) into natural {$langName}.
3. Return ONLY a valid JSON object with key "translated" containing an array of strings in the exact same order.
4. No markdown ticks, raw JSON only.
PROMPT;

        try {
            $response = Http::timeout(15)
                ->withToken($this->apiKey)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => json_encode($titles, JSON_UNESCAPED_UNICODE)],
                    ],
                    'response_format' => ['type' => 'json_object'],
                    'temperature' => 0.0,
                ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content', '{}');
                $content = preg_replace('/<think>[\s\S]*?<\/think>/u', '', $content);
                $content = preg_replace('/^```(?:json)?\s*([\s\S]*?)\s*```$/u', '$1', trim($content));

                $decoded = json_decode($content, true);
                if (is_array($decoded) && isset($decoded['translated']) && is_array($decoded['translated']) && count($decoded['translated']) === count($titles)) {
                    return $decoded['translated'];
                }
            }
        } catch (\Exception $e) {
            Log::error('Groq API Exception in translateProductsBack: ' . $e->getMessage());
        }

        return $titles;
    }

    /**
     * Безопасный фоллбек в случае недоступности ИИ
     */
    protected function fallbackAnalysis(string $query, string $sourceLang = 'en'): array
    {
        return [
            'intent' => 'product_search',
            'source_language' => $sourceLang,
            'greek_query' => $query,
            'brand' => null,
            'filters' => [
                'price_min' => null,
                'price_max' => null,
                'sort' => 'relevance',
                'required_features' => [],
            ],
        ];
    }
}