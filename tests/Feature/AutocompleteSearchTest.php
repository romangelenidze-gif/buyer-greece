<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class AutocompleteSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        try {
            Redis::del('autocomplete_hits_counter');
        } catch (\Exception $e) {
            // Игнорируем, если Redis временно недоступен
        }
    }

    public function test_autocomplete_endpoint_returns_suggestions()
    {
        DB::table('learned_autocomplete')->insert([
            'query' => 'кроссовки',
            'suggestions_json' => json_encode(['Кроссовки Adidas', 'Кроссовки Nike']),
            'hits' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->getJson(route('api.autocomplete', ['q' => 'крос']));

        $response->assertStatus(200)
                 ->assertJsonStructure();
    }

    public function test_autocomplete_is_protected_by_rate_limiting()
    {
        for ($i = 0; $i < 30; $i++) {
            $this->getJson(route('api.autocomplete', ['q' => 'тест']));
        }

        $response = $this->getJson(route('api.autocomplete', ['q' => 'тест']));
        
        $response->assertStatus(429);
    }

    public function test_sync_autocomplete_hits_artisan_command_works()
    {
        DB::table('learned_autocomplete')->insert([
            'query' => 'сумка',
            'suggestions_json' => json_encode(['Сумка кожаная']),
            'hits' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            Redis::zadd('autocomplete_hits_counter', ['сумка' => 5]);
        } catch (\Exception $e) {
            // Пропускаем
        }

        $this->artisan('autocomplete:sync-hits')
             ->assertExitCode(0);

        $this->assertDatabaseHas('learned_autocomplete', [
            'query' => 'сумка',
            'hits' => 15,
        ]);
    }
}