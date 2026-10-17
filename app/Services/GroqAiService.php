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

    public function analyzeQuery(string $userQuery): array
    {
        $cleanQuery = trim($userQuery);

        $isRussianOrGeorgian = preg_match('/[\x{0400}-\x{04FF}\x{10A0}-\x{10FF}]/u', $cleanQuery);
        
        if (!$isRussianOrGeorgian) {
            return ['greek_query' => $cleanQuery];
        }

        if (empty($this->apiKey)) {
            return ['greek_query' => $cleanQuery];
        }

        $systemPrompt = 'You are an elite e-commerce AI search assistant and spell-checker for BestPrice.gr. ' .
            'Your tasks for any user query in Russian or Georgian (which may contain typos or spelling errors): ' .
            '1. Auto-correct typos and spelling mistakes. ' .
            '2. Translate and adapt into natural Greek e-commerce keywords. ' .
            'Examples:\n' .
            '- ქალის ჩנתა -> γυναικεία τσάντα\n' .
            '- женская куртка -> γυναικείο μπουφάν\n' .
            'Return ONLY a valid JSON object with key "greek_query". No markdown, raw JSON only.';

        try {
            $response = Http::timeout(15)
                ->withToken($this->apiKey)
                ->withoutVerifying()
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
                    return ['greek_query' => $decoded['greek_query']];
                }
            }
        } catch (\Exception $e) {
            Log::error('Groq API exception: ' . $e->getMessage());
        }

        return ['greek_query' => $cleanQuery];
    }

    /**
     * Пакетный перевод названий товаров обратно на язык пользователя (RU/KA)
     */
    public function translateProductsBack(array $titles, string $targetLanguage): array
    {
        if (empty($titles) || in_array($targetLanguage, ['el', 'en'])) {
            return $titles;
        }

        $langName = $targetLanguage === 'ru' ? 'Russian' : ($targetLanguage === 'ka' ? 'Georgian' : 'English');

        $systemPrompt = "You are a professional e-commerce translator. Translate the following Greek product titles into {$langName}. " .
            "Strict rules:\n" .
            "1. Keep brand names (like Adidas, Nike, Apple, Guess, Samsung), model names, product codes, dimensions, and technical units (ml, kg, GB, cm) strictly in their original Latin characters or numbers.\n" .
            "2. Translate descriptive words (like jacket, shoes, waterproof, black) into natural {$langName}.\n" .
            "Return ONLY a valid JSON object with key 'translated' containing an array of strings in the exact same order. No markdown, raw JSON only.";

        try {
            $response = Http::timeout(15)
                ->withToken($this->apiKey)
                ->withoutVerifying()
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
                if (is_array($decoded) && isset($decoded['translated']) && is_array($decoded['translated'])) {
                    return $decoded['translated'];
                }
            }
        } catch (\Exception $e) {
            Log::error('Groq batch translation exception: ' . $e->getMessage());
        }

        return $titles;
    }
}