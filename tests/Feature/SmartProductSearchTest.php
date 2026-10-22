<?php

namespace Tests\Feature;

use App\Services\SmartProductSearchManager;
use Tests\TestCase;

class SmartProductSearchTest extends TestCase
{
    public function test_it_translates_query_and_calls_mcp()
    {
        $manager = app(SmartProductSearchManager::class);

        // Передаем простой запрос на русском
        $result = $manager->search('помада', '10431');

        // Проверяем, что перевод отработал
        $this->assertNotEmpty($result['greek_query']);
        
        // Проверяем структуру ответа MCP
        $this->assertIsArray($result['mcp_response']);
        
        dump('Full Smart Search Result:', $result);
    }
}