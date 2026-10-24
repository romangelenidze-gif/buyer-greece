<?php

namespace App\Services;

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

        // 1. Умный поиск в собственной базе (частичное совпадение / ассоциация)
        // Ищем запросы, которые содержат введенный текст или начинаются с него
        $learned = DB::table('learned_autocomplete')
            ->where('query', 'LIKE', '%' . $normalized . '%')
            ->orderBy('hits', 'desc')
            ->first();

        if ($learned) {
            // Увеличиваем счетчик популярности и обновляем время в фоновом режиме
            DB::table('learned_autocomplete')->where('id', $learned->id)->update([
                'hits' => DB::raw('hits + 1'),
                'last_used_at' => now(),
            ]);

            $suggestions = json_decode($learned->suggestions_json, true);
            if (is_array($suggestions) && !empty($suggestions)) {
                return $suggestions;
            }
        }

        // 2. Если в базе ничего похожего нет — запрашиваем у Groq AI (первое торможение)
        $aiSuggestions = $this->fetchAiSuggestions($rawQuery, $locale);

        if (!empty($aiSuggestions)) {
            // Сохраняем в собственную базу для мгновенного ответа в будущем
            try {
                DB::table('learned_autocomplete')->updateOrInsert(
                    ['query' => $normalized],
                    [
                        'suggestions_json' => json_encode($aiSuggestions, JSON_UNESCAPED_UNICODE),
                        'hits' => 1,
                        'last_used_at' => now(),
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            } catch (\Exception $e) {
                Log::warning('Failed to save learned autocomplete: ' . $e->getMessage());
            }
        }

        return $aiSuggestions;
    }

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