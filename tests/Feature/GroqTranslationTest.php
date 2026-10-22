<?php

namespace Tests\Feature;

use App\Services\GroqAiService;
use Tests\TestCase;

class GroqTranslationTest extends TestCase
{
    public function test_it_translates_user_queries_to_greek()
    {
        $service = app(GroqAiService::class);

        // Тестируем перевод слова "помада"
        $result = $service->analyzeQuery('помада');

        dump('Query Result for "помада":', $result);

        $this->assertNotEmpty($result['greek_query']);
        
        // Проверяем, что результат содержит греческие символы
        $this->assertMatchesRegularExpression('/[\x{0370}-\x{03FF}]/u', $result['greek_query'], 'Перевод должен быть на греческом языке');
    }
}