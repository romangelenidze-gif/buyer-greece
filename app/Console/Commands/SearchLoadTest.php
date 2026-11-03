<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class SearchLoadTest extends Command
{
    protected $signature = 'search:load-test {users=100}';
    protected $description = 'Симуляция нагрузки на поиск (автокомплит и выдача)';

    public function handle()
    {
        $users = (int) $this->argument('users');
        $this->info("Запуск нагрузочного теста для {$users} виртуальных пользователей...");

        $queries = ['admiral обувь', 'nike', 'iphone 15', 'adidas', 'часы', 'наушники'];
        $baseUrl = 'http://192.168.1.53:8000';

        $start = microtime(true);
        
        // Выполняем параллельные запросы пакетами
        $responses = Http::pool(function ($pool) use ($users, $queries, $baseUrl) {
            $requests = [];
            for ($i = 0; $i < $users; $i++) {
                $q = $queries[array_rand($queries)];
                
                // Симулируем 3 запроса от одного пользователя в рамках пула
                $requests[] = $pool->get("{$baseUrl}/api/autocomplete?q=ad");
                $requests[] = $pool->get("{$baseUrl}/product-search?q=" . urlencode($q));
                $requests[] = $pool->get("{$baseUrl}/api/autocomplete?q=ip");
            }
            return $requests;
        });

        $duration = microtime(true) - $start;
        $totalRequests = count($responses);
        
        // Фильтруем корректно: проверяем, что это Response и статус успешный
        $success = collect($responses)->filter(function ($r) {
            return $r instanceof Response && $r->successful();
        })->count();
        
        $failed = $totalRequests - $success;

        $this->info("--- Результаты теста ---");
        $this->info("Всего запросов: {$totalRequests}");
        $this->info("Время выполнения: " . round($duration, 2) . " сек.");
        $this->info("Успешных (HTTP 200): {$success}");
        $this->info("Ошибок: {$failed}");
    }
}