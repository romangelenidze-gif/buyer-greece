<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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

    public function getSuggestions(string $query, string $locale = 'ru'): array
    {
        $rawQuery = trim($query);
        $normalized = mb_strtolower($rawQuery);

        if (mb_strlen($normalized) < 2) {
            return [];
        }

        $cacheKey = "autocomplete:v1:{$locale}:{$normalized}";

        // Кэшируем только в кэш приложения/Redis. Не совершаем запись в DB при вводе!
        return Cache::remember($cacheKey, now()->addHours(24), function () use ($normalized, $locale, $rawQuery) {
            
            // 1. Поиск среди реально популярных завершенных запросов
            $learned = DB::table('learned_autocomplete')
                ->where('query', 'LIKE', $normalized . '%')
                ->orderBy('hits', 'desc')
                ->first();

            if ($learned && !empty($learned->suggestions_json)) {
                $suggestions = json_decode($learned->suggestions_json, true);
                if (is_array($suggestions) && !empty($suggestions)) {
                    return $suggestions;
                }
            }

            // 2. Запрос к ИИ Groq, если ничего не найдено в базе
            return $this->fetchAiSuggestions($rawQuery, $locale);
        });
    }

    protected function fetchAiSuggestions(string $query, string $locale): array
    {
        if (empty($this->apiKey)) {
            return [];
        }

        $langName = match ($locale) {
            'ka' => 'Georgian',
            'en' => 'English',
            default => 'Russian',
        };

        $prompt = <<<PROMPT
You are a smart e-commerce search autocomplete assistant.
The user is typing: "{$query}".
Provide 3-4 smart search suggestions in {$langName}.

CRITICAL RULES:
1. "label": Clean display text translated to {$langName}.
2. "value": Clean search query string in {$langName} or Latin brand/model names (e.g., "New Balance 574", "Adidas Samba").
3. "badge": Short category badge translated strictly into {$langName} (e.g., in Georgian: "ბრენდი", "კატეგორია", "მოდელი"; in English: "Brand", "Category", "Model"; in Russian: "Бренд", "Категория", "Модель").

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