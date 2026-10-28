<?php

namespace App\Console\Commands;

use App\Services\BestPriceMcpService;
use App\Services\GroqAiService;
use App\Services\SmartProductSearchManager;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Положить в app/Console/Commands/SearchDiagnose.php
 *
 * Запуск:
 *   php artisan search:diagnose
 *   php artisan search:diagnose "кроссовки найк мужские 42 до 80 евро"
 *   php artisan search:diagnose "часы" --mcp-query="iPhone 15"
 *
 * Каждый этап проверяется отдельно и печатает СЫРОЙ ответ, чтобы было видно, где ломается цепочка.
 */
class SearchDiagnose extends Command
{
    protected $signature = 'search:diagnose {query? : Запрос пользователя} {--locale=ru} {--mcp-query=iPhone 15 : Запрос для сырого теста MCP}';
    protected $description = 'Пошаговая диагностика умного поиска: конфиг, Groq, MCP BestPrice, сервисы, менеджер, логи';

    protected array $problems = [];

    public function handle(): int
    {
        $query = $this->argument('query') ?: 'кроссовки найк мужские 42 до 80 евро';

        $this->info('Запрос: ' . $query);

        $this->step('1. Конфиг и окружение', fn () => $this->checkConfig());
        $this->step('2. Groq: сырой запрос и список моделей', fn () => $this->checkGroqRaw());
        $analysis = null;
        $this->step('3. GroqAiService::analyzeQuery', function () use ($query, &$analysis) {
            $analysis = $this->checkAnalysis($query);
        });
        $this->step('4. MCP BestPrice: сырой протокол', fn () => $this->checkMcpRaw((string) $this->option('mcp-query')));
        $this->step('5. BestPriceMcpService::searchProducts', fn () => $this->checkMcpService($analysis));
        $this->step('6. Весь конвейер (SmartProductSearchManager, без кэша)', fn () => $this->checkManager($query));
        $this->step('7. Последние записи в логе', fn () => $this->tailLog());

        $this->newLine();
        $this->info('================ ИТОГ ================');

        if ($this->problems === []) {
            $this->info('Явных проблем не найдено. Если в интерфейсе всё равно пусто, проблема во view / контроллере / фронте.');
        } else {
            foreach ($this->problems as $problem) {
                $this->error('• ' . $problem);
            }
        }

        return self::SUCCESS;
    }

    // ------------------------------------------------------------------ //

    protected function checkConfig(): void
    {
        $key = (string) config('services.groq.api_key', '');
        $model = config('services.groq.model');

        $this->line('PHP: ' . PHP_VERSION . ' | Laravel: ' . app()->version());
        $this->line('GROQ api_key в config: ' . ($key === '' ? 'ПУСТО' : substr($key, 0, 6) . '… (' . strlen($key) . ' симв.)'));
        $this->line('GROQ_API_KEY в .env: ' . (env('GROQ_API_KEY') ? 'есть' : 'нет'));
        $this->line('Модель в config: ' . ($model ?: '(не задана, будет openai/gpt-oss-20b)'));
        $this->line('Эндпоинт MCP: ' . config('services.bestprice.mcp_endpoint', 'https://mcp.bestprice.gr/mcp (по умолчанию)'));
        $this->line('Кэш по умолчанию: ' . config('cache.default'));

        if ($key === '') {
            $this->problems[] = 'config(services.groq.api_key) пустой. Новый GroqAiService не читает env() напрямую: добавьте в config/services.php  \'groq\' => [\'api_key\' => env(\'GROQ_API_KEY\'), \'model\' => env(\'GROQ_MODEL\', \'openai/gpt-oss-20b\')]  и выполните php artisan config:clear. Из-за этого анализ запроса молча отключается.';
        }

        if (version_compare(app()->version(), '9.0.0', '<')) {
            $this->problems[] = 'Laravel < 9: именованный параметр throw: в Http::retry() может не поддерживаться. Замените в GroqAiService вызов retry(...) на retry(2, 300).';
        }
    }

    protected function checkGroqRaw(): void
    {
        $key = (string) config('services.groq.api_key', '');
        $model = (string) (config('services.groq.model') ?: 'openai/gpt-oss-20b');

        if ($key === '') {
            $this->warn('Пропуск: ключа нет.');

            return;
        }

        $models = Http::timeout(15)->withToken($key)->get('https://api.groq.com/openai/v1/models');
        $this->line('GET /models -> HTTP ' . $models->status());

        if ($models->successful()) {
            $ids = collect($models->json('data', []))->pluck('id')->all();
            $this->line('Модель "' . $model . '" ' . (in_array($model, $ids, true) ? 'СУЩЕСТВУЕТ' : 'НЕ НАЙДЕНА'));

            if (!in_array($model, $ids, true)) {
                $this->problems[] = 'Модель "' . $model . '" отсутствует в списке Groq. Подходящие: ' . implode(', ', array_filter($ids, fn ($id) => preg_match('/qwen|gpt-oss|llama/i', $id)));
            }
        } else {
            $this->line(Str::limit($models->body(), 400));
            $this->problems[] = 'Groq /models вернул HTTP ' . $models->status() . ': неверный ключ или нет доступа.';
        }

        $payload = [
            'model' => $model,
            'messages' => [['role' => 'user', 'content' => 'Return JSON {"ok": true}']],
            'response_format' => ['type' => 'json_object'],
            'temperature' => 0,
        ];

        $response = Http::timeout(20)->withToken($key)->post('https://api.groq.com/openai/v1/chat/completions', $payload);
        $this->line('POST /chat/completions -> HTTP ' . $response->status());
        $this->line(Str::limit($response->body(), 600));

        if (!$response->successful()) {
            $this->problems[] = 'Groq chat/completions вернул HTTP ' . $response->status() . ' (см. тело выше).';
        }
    }

    protected function checkAnalysis(string $query): array
    {
        $analysis = app(GroqAiService::class)->analyzeQuery($query);

        $this->line(json_encode($analysis, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        if (!empty($analysis['degraded'])) {
            $this->problems[] = 'analyzeQuery вернул degraded=true: LLM не отработала (причина в шагах 1-2 или в логе, шаг 7).';
        }

        return $analysis;
    }

    protected function checkMcpRaw(string $mcpQuery): void
    {
        $endpoint = (string) config('services.bestprice.mcp_endpoint', 'https://mcp.bestprice.gr/mcp');
        $headers = [
            'Accept' => 'application/json, text/event-stream',
            'Content-Type' => 'application/json',
        ];

        $init = Http::timeout(15)->withHeaders($headers)->post($endpoint, [
            'jsonrpc' => '2.0',
            'method' => 'initialize',
            'params' => [
                'protocolVersion' => '2025-03-26',
                'capabilities' => new \stdClass(),
                'clientInfo' => ['name' => 'diagnose', 'version' => '1.0'],
            ],
            'id' => 1,
        ]);

        $sessionId = $init->header('Mcp-Session-Id') ?: null;
        $this->line('initialize -> HTTP ' . $init->status() . ' | Content-Type: ' . $init->header('Content-Type') . ' | Mcp-Session-Id: ' . ($sessionId ?: '(нет)'));
        $this->line(Str::limit($init->body(), 700));

        if (!$init->successful()) {
            $this->problems[] = 'MCP initialize вернул HTTP ' . $init->status() . ': сервер недоступен или отверг запрос (см. тело выше).';

            return;
        }

        $initData = $this->parseRpc($init);
        $version = $initData['result']['protocolVersion'] ?? '2025-03-26';

        $headers['MCP-Protocol-Version'] = $version;
        if ($sessionId) {
            $headers['Mcp-Session-Id'] = $sessionId;
        }

        $notify = Http::timeout(10)->withHeaders($headers)->post($endpoint, [
            'jsonrpc' => '2.0',
            'method' => 'notifications/initialized',
        ]);
        $this->line('notifications/initialized -> HTTP ' . $notify->status());

        $list = Http::timeout(15)->withHeaders($headers)->post($endpoint, [
            'jsonrpc' => '2.0',
            'method' => 'tools/list',
            'id' => 2,
        ]);
        $this->line('tools/list -> HTTP ' . $list->status());

        $listData = $this->parseRpc($list);
        $tools = $listData['result']['tools'] ?? [];
        $this->line('Инструменты: ' . implode(', ', array_column($tools, 'name')));

        $search = collect($tools)->firstWhere('name', 'search_products');

        if ($search === null) {
            $this->problems[] = 'В tools/list нет инструмента search_products (список выше).';
        } else {
            $this->line('inputSchema search_products:');
            $this->line(json_encode($search['inputSchema'] ?? $search, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        $call = Http::timeout(30)->withHeaders($headers)->post($endpoint, [
            'jsonrpc' => '2.0',
            'method' => 'tools/call',
            'params' => ['name' => 'search_products', 'arguments' => ['query' => $mcpQuery, 'limit' => 3]],
            'id' => 3,
        ]);
        $this->line('tools/call search_products("' . $mcpQuery . '") -> HTTP ' . $call->status() . ' | Content-Type: ' . $call->header('Content-Type'));
        $this->line(Str::limit($call->body(), 1500));

        if (!$call->successful()) {
            $this->problems[] = 'MCP tools/call вернул HTTP ' . $call->status() . ' (см. тело выше).';
        }
    }

    protected function checkMcpService(?array $analysis): void
    {
        $service = app(BestPriceMcpService::class);
        $queries = array_filter([
            $analysis['query_el'] ?? null,
            $analysis['query_en'] ?? null,
            $analysis['relaxed_el'] ?? null,
        ]);

        if ($queries === []) {
            $queries = ['iPhone 15'];
        }

        foreach (array_unique($queries) as $q) {
            $result = $service->searchProducts($q, ['limit' => 8]);
            $this->line(sprintf(
                '"%s" -> ok=%s, товаров=%d%s',
                $q,
                $result['ok'] ? 'true' : 'false',
                count($result['products']),
                $result['error'] ? ', ошибка: ' . Str::limit($result['error'], 300) : ''
            ));

            foreach (array_slice($result['products'], 0, 3) as $p) {
                $this->line('    - ' . Str::limit((string) ($p['title'] ?? json_encode($p, JSON_UNESCAPED_UNICODE)), 100));
            }

            if (!$result['ok']) {
                $this->problems[] = 'BestPriceMcpService не смог выполнить "' . $q . '": ' . Str::limit((string) $result['error'], 200);
            }
        }
    }

    protected function checkManager(string $query): void
    {
        $manager = app(SmartProductSearchManager::class);

        // run() защищён и не использует кэш: вызываем напрямую.
        $method = new \ReflectionMethod($manager, 'run');
        $method->setAccessible(true);

        $lang = GroqAiService::detectLanguage($query);
        $lang = in_array($lang, ['ru', 'ka', 'el'], true) ? $lang : (string) $this->option('locale');

        $result = $method->invoke($manager, $query, $lang, []);

        $this->line('recommendation: ' . ($result['recommendation'] ?? ''));
        $this->line('needs_clarification: ' . (!empty($result['needs_clarification']) ? 'да' : 'нет'));
        $this->line('товаров: ' . count($result['products'] ?? []));
        $this->line('search_meta: ' . json_encode($result['search_meta'] ?? null, JSON_UNESCAPED_UNICODE));
        $this->line('route: ' . ($result['intent_analysis']['source'] ?? '?') . ' | model: ' . ($result['intent_analysis']['model'] ?? '-'));
        $this->line('active_filters: ' . json_encode($result['active_filters'] ?? null, JSON_UNESCAPED_UNICODE));

        foreach (array_slice($result['products'] ?? [], 0, 3) as $p) {
            $this->line('    - ' . Str::limit((string) $p['title'], 80) . ' | ' . ($p['price_formatted'] ?? '') . ' | ' . ($p['product_url'] ?? ''));
        }

        if (empty($result['products']) && empty($result['needs_clarification'])) {
            $this->problems[] = 'Конвейер вернул пустую выдачу: "' . ($result['recommendation'] ?? '') . '"';
        }
    }

    protected function tailLog(): void
    {
        $this->printLogTail(storage_path('logs/laravel.log'), '/BestPrice|Groq|SmartProductSearch/i', 8, 'laravel.log (ошибки Groq/BestPrice)');

        $searchLogs = glob(storage_path('logs/search*.log')) ?: [];

        if ($searchLogs === []) {
            $this->line('Журнала поисков ещё нет: сделайте поиск в браузере.');

            return;
        }

        usort($searchLogs, fn ($a, $b) => filemtime($b) <=> filemtime($a));
        $this->printLogTail($searchLogs[0], null, 8, basename($searchLogs[0]) . ' (журнал поисков: route=local|llm|llm_cache|result_cache)');
    }

    protected function printLogTail(string $file, ?string $pattern, int $count, string $title): void
    {
        $this->line('[' . $title . ']');

        if (!is_file($file)) {
            $this->line('  файла нет');

            return;
        }

        $handle = fopen($file, 'rb');
        fseek($handle, max(0, filesize($file) - 300000));
        $chunk = (string) stream_get_contents($handle);
        fclose($handle);

        $lines = array_filter(
            preg_split('/\r?\n/', $chunk) ?: [],
            fn ($line) => trim($line) !== '' && ($pattern === null || preg_match($pattern, $line))
        );

        if ($lines === []) {
            $this->line('  записей нет');

            return;
        }

        foreach (array_slice($lines, -$count) as $line) {
            $this->line('  ' . Str::limit($line, 350));
        }
    }

    // ------------------------------------------------------------------ //

    protected function step(string $title, callable $callback): void
    {
        $this->newLine();
        $this->info('--- ' . $title . ' ---');

        try {
            $callback();
        } catch (\Throwable $e) {
            $this->error('ИСКЛЮЧЕНИЕ: ' . get_class($e) . ': ' . $e->getMessage());
            $this->line($e->getFile() . ':' . $e->getLine());
            $this->problems[] = 'Шаг "' . $title . '" упал с исключением: ' . $e->getMessage();
        }
    }

    protected function parseRpc(Response $response): ?array
    {
        $type = strtolower((string) $response->header('Content-Type'));

        if (str_contains($type, 'text/event-stream')) {
            foreach (preg_split('/\r?\n/', $response->body()) as $line) {
                if (str_starts_with($line, 'data:')) {
                    $json = json_decode(trim(substr($line, 5)), true);

                    if (is_array($json)) {
                        return $json;
                    }
                }
            }

            return null;
        }

        $json = $response->json();

        return is_array($json) ? $json : null;
    }
}
