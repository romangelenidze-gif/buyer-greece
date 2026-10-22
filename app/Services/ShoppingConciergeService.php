<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShoppingConciergeService
{
    protected string $apiKey;
    protected string $model = 'openai/gpt-oss-20b';

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key', env('GROQ_API_KEY', ''));
    }

    /**
     * Сравнивает выбранные карточки товаров и дает экспертную рекомендацию на основе реальных данных.
     */
    public function compareProducts(array $selectedProducts, string $userQuestion = '', string $locale = 'ru'): array
    {
        if (count($selectedProducts) < 2) {
            return [
                'success' => false,
                'message' => __('Выберите минимум 2 товара для сравнения.'),
            ];
        }

        $langName = $locale === 'ka' ? 'Georgian' : ($locale === 'ru' ? 'Russian' : 'English');

        $systemPrompt = <<<PROMPT
You are Buyer Greece AI Shopping Concierge. 
Compare the provided products based ONLY on their real titles, prices, and available parameters.
Do NOT invent non-existent specs, discounts, or fake reviews.
Provide a clear comparative summary in {$langName} answering which item fits best for what need, and highlight key differences.
Format your output as structured JSON:
{
   "comparison_table": [
       {"feature": "Name/Title", "values": ["Prod 1", "Prod 2"]},
       {"feature": "Price", "values": ["€100", "€120"]}
   ],
   "recommendation": "Your objective text recommendation here..."
}
PROMPT;

        try {
            $response = Http::timeout(20)
                ->withToken($this->apiKey)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => json_encode(['products' => $selectedProducts, 'user_question' => $userQuestion], JSON_UNESCAPED_UNICODE)],
                    ],
                    'response_format' => ['type' => 'json_object'],
                    'temperature' => 0.2,
                ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content', '{}');
                $content = preg_replace('/<think>[\s\S]*?<\/think>/u', '', $content);
                $content = preg_replace('/^```(?:json)?\s*([\s\S]*?)\s*```$/u', '$1', trim($content));

                $decoded = json_decode($content, true);
                if (is_array($decoded)) {
                    return [
                        'success' => true,
                        'data' => $decoded,
                    ];
                }
            } else {
                Log::error('Concierge Comparison API Error: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Concierge Comparison Exception: ' . $e->getMessage());
        }

        return [
            'success' => false,
            'message' => __('Не удалось выполнить сравнение через ИИ-консьержа.'),
        ];
    }

    /**
     * Подбор аналогов или более дешевых вариантов для конкретного товара
     */
    public function findAlternatives(array $product, string $goal = 'cheaper', string $locale = 'ru'): array
    {
        $title = $product['title'] ?? '';
        $price = $product['price_amount'] ?? 0;
        $langName = $locale === 'ka' ? 'Georgian' : ($locale === 'ru' ? 'Russian' : 'English');

        $systemPrompt = <<<PROMPT
You are Buyer Greece AI Shopping Concierge. 
The user wants an alternative to this product based on the goal.
Provide search keywords or a reformulated query in Greek to find this alternative, and explain in {$langName} what to look for.
Return ONLY valid JSON:
{
    "greek_query_alternative": "...",
    "explanation": "..."
}
PROMPT;

        $userPayload = json_encode([
            'product_title' => $title,
            'price' => $price,
            'goal' => $goal
        ], JSON_UNESCAPED_UNICODE);

        try {
            $response = Http::timeout(15)
                ->withToken($this->apiKey)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPayload],
                    ],
                    'response_format' => ['type' => 'json_object'],
                    'temperature' => 0.2,
                ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content', '{}');
                $content = preg_replace('/<think>[\s\S]*?<\/think>/u', '', $content);
                $content = preg_replace('/^```(?:json)?\s*([\s\S]*?)\s*```$/u', '$1', trim($content));

                $decoded = json_decode($content, true);
                if (is_array($decoded)) {
                    return [
                        'success' => true,
                        'data' => $decoded,
                    ];
                }
            } else {
                Log::error('Concierge Alternatives API Error: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Concierge Alternatives Exception: ' . $e->getMessage());
        }

        return [
            'success' => false,
            'message' => __('Не подобрать аналоги через ИИ.'),
        ];
    }
}