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
        $this->model = config('services.groq.model', 'qwen/qwen3.8-27b');
    }

    public function analyzeQuery(string $userQuery): array
    {
        $cleanQuery = trim($userQuery);

        if (empty($cleanQuery) || empty($this->apiKey)) {
            return $this->fallbackAnalysis($cleanQuery);
        }

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
Analyze the user query and extract structured search parameters.

Strict Rules:
1. "greek_query": Correct typos, translate descriptive terms into standard, highly accurate commercial Greek search keywords (e.g., 'помада' -> 'κραγιόν', 'румяна' -> 'ρουζ', 'кроссовки' -> 'αθλητικά παπούτσια'). KEEP BRAND NAMES in Latin script. Do NOT include price terms in "greek_query".
2. "alternative_queries": Array of 1-2 Greek alternative search synonyms or category terms.
3. "source_language": Identified language ("ru", "ka", "en", or "el").
4. "intent": One of "product_search", "price_check", or "recommendation".
5. "brand": Brand name in Latin if present, otherwise null.
6. "filters": Object containing:
   - "price_min": float number or null
   - "price_max": float number or null
   - "sort": Allowed string ONLY: "relevance", "price_asc", or "price_desc" (null if not specified)
   - "required_features": Array of key terms/attributes in Greek or empty array []
7. Return ONLY a valid JSON object matching this schema. No markdown formatting, no text before or after JSON.
PROMPT;

        try {
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
                    $sort = $decoded['filters']['sort'] ?? 'relevance';
                    if (!in_array($sort, ['relevance', 'price_asc', 'price_desc'])) {
                        $sort = 'relevance';
                    }

                    return [
                        'intent' => $decoded['intent'] ?? 'product_search',
                        'source_language' => $decoded['source_language'] ?? $sourceLang,
                        'greek_query' => trim($decoded['greek_query']),
                        'alternative_queries' => is_array($decoded['alternative_queries'] ?? null) ? $decoded['alternative_queries'] : [],
                        'brand' => $decoded['brand'] ?? null,
                        'filters' => [
                            'price_min' => isset($decoded['filters']['price_min']) ? (float)$decoded['filters']['price_min'] : null,
                            'price_max' => isset($decoded['filters']['price_max']) ? (float)$decoded['filters']['price_max'] : null,
                            'sort' => $sort,
                            'required_features' => is_array($decoded['filters']['required_features'] ?? null) ? $decoded['filters']['required_features'] : [],
                        ],
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error('Groq API Exception in analyzeQuery: ' . $e->getMessage());
        }

        return $this->fallbackAnalysis($cleanQuery, $sourceLang);
    }

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
1. KEEP ALL BRAND NAMES, model codes, serial numbers, dimensions, and technical units strictly in Latin characters/numbers.
2. Translate only descriptive general words into natural {$langName}.
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
                        ['role' => 'user', 'content' => json_encode(array_values($titles), JSON_UNESCAPED_UNICODE)],
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

    protected function fallbackAnalysis(string $query, string $sourceLang = 'en'): array
    {
        return [
            'intent' => 'product_search',
            'source_language' => $sourceLang,
            'greek_query' => $query,
            'alternative_queries' => [],
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