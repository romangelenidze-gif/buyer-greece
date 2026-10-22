<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductAutocompleteService
{
    protected string $apiKey;
    protected string $model = 'openai/gpt-oss-20b';

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key', env('GROQ_API_KEY', ''));
    }

    /**
     * Возвращает список умных подсказок на основе запроса с привлечением ИИ.
     */
    public function getSuggestions(string $query, string $locale = 'ru'): array
    {
        $rawQuery = trim($query);
        $normalized = mb_strtolower($rawQuery);

        if (mb_strlen($normalized) < 2) {
            return [];
        }

        // 1. Быстрый локальный словарь брендов и категорий
        $dictionary = [
            ['label' => 'New Balance 574', 'value' => 'New Balance 574', 'badge' => 'Модель'],
            ['label' => 'New Balance 990', 'value' => 'New Balance 990', 'badge' => 'Модель'],
            ['label' => 'Adidas', 'value' => 'Adidas', 'badge' => 'Бренд'],
            ['label' => 'Nike', 'value' => 'Nike', 'badge' => 'Бренд'],
            ['label' => 'Apple iPhone', 'value' => 'iPhone', 'badge' => 'Бренд'],
            ['label' => 'Samsung', 'value' => 'Samsung', 'badge' => 'Бренд'],
            ['label' => 'Смартфоны', 'value' => 'смартфон', 'badge' => 'Категория'],
            ['label' => 'Кроссовки', 'value' => 'кроссовки', 'badge' => 'Категория'],
            ['label' => 'Ноутбуки', 'value' => 'ноутбук', 'badge' => 'Категория'],
        ];

        $localMatches = [];
        foreach ($dictionary as $item) {
            if (mb_strpos(mb_strtolower($item['label']), $normalized) !== false || mb_strpos(mb_strtolower($item['value']), $normalized) !== false) {
                $localMatches[] = $item;
            }
        }

        if (count($localMatches) >= 3) {
            return array_slice($localMatches, 0, 5);
        }

        // 2. Умная обработка через Groq AI (с кэшированием на 24 часа)
        $cacheKey = 'autocomplete_ai_' . md5($normalized . '_' . $locale);

        $aiSuggestions = Cache::remember($cacheKey, 86400, function () use ($rawQuery, $locale) {
            return $this->fetchAiSuggestions($rawQuery, $locale);
        });

        $all = array_merge($localMatches, $aiSuggestions);

        $unique = [];
        foreach ($all as $item) {
            $key = mb_strtolower($item['value']);
            if (!isset($unique[$key])) {
                $unique[$key] = $item;
            }
        }

        return array_values(array_slice($unique, 0, 6));
    }

    /**
     * Вызов Groq AI: поле value теперь содержит чистый поисковый запрос (без греческого алфавита в инпуте).
     */
    protected function fetchAiSuggestions(string $query, string $locale): array
    {
        if (empty($this->apiKey)) {
            return [];
        }

        $langName = $locale === 'ka' ? 'Georgian' : ($locale === 'ru' ? 'Russian' : 'English');

        $prompt = <<<PROMPT
You are a smart e-commerce search autocomplete assistant.
The user is typing: "{$query}".
Provide 3-4 smart search suggestions.
CRITICAL RULES:
1. "label": Clean display text in {$langName} for the user.
2. "value": The clean search query string in {$langName} or Latin brand/model names (e.g., "New Balance 574", "Adidas Samba"). DO NOT put Greek alphabet strings into "value".
3. "badge": Short category like "Бренд", "Категория", "Модель".
Return ONLY a valid JSON object with a "suggestions" key containing an array of objects:
{
  "suggestions": [
    {"label": "...", "value": "...", "badge": "..."}
  ]
}
PROMPT;

        try {
            $response = Http::timeout(4)
                ->withToken($this->apiKey)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'response_format' => ['type' => 'json_object'],
                    'temperature' => 0.1,
                ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content', '{}');
                $content = preg_replace('/<think>[\s\S]*?<\/think>/u', '', $content);
                $content = preg_replace('/^```(?:json)?\s*([\s\S]*?)\s*```$/u', '$1', trim($content));

                $decoded = json_decode($content, true);
                
                if (isset($decoded['suggestions']) && is_array($decoded['suggestions'])) {
                    return $decoded['suggestions'];
                }
            }
        } catch (\Exception $e) {
            Log::warning('Autocomplete AI fetch error: ' . $e->getMessage());
        }

        return [];
    }
}