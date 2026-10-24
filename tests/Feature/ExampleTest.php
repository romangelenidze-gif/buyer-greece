<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase; // <-- 1. Импортируем трейт
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase; // <-- 2. Подключаем его здесь

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}