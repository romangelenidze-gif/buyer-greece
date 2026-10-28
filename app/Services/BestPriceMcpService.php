<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Клиент MCP BestPrice.gr (Streamable HTTP).
 *
 * Возвращает ['ok' => bool, 'products' => array, 'error' => ?string], чтобы вызывающий код
 * отличал «ничего не найдено» (ok=true, products=[]) от «сервис сломался» (ok=false).
 *
 * Для просмотра реальной схемы инструмента:
 *   php artisan tinker
 *   >>> app(\App\Services\BestPriceMcpService::class)->listTools()
 */
class BestPriceMcpService
{
    protected const SESSION_CACHE_KEY = 'bestprice_mcp_session_v1';
    protected const SESSION_TTL = 600;
    protected const PROTOCOL_VERSION = '2025-03-26';
    protected const MAX_LIMIT = 8;
    protected const COOLDOWN_CACHE_KEY = 'bestprice_mcp_cooldown';

    protected string $endpoint;
    protected int $cacheTtl;
    protected int $cooldownSeconds;

    public function __construct()
    {
        $this->endpoint = (string) config('services.bestprice.mcp_endpoint', 'https://mcp.bestprice.gr/mcp');
        $this->cacheTtl = (int) config('services.bestprice.cache_ttl', 10800);        // 3 часа
        $this->cooldownSeconds = (int) config('services.bestprice.cooldown', 20);     // пауза после HTTP 429
    }

    public function searchProducts(string $query, array $options = []): array
    {
        $query = trim($query);

        if ($query === '') {
            return $this->result(false, [], 'empty query');
        }

        $arguments = ['query' => $query];

        if (isset($options['price_min']) && (float) $options['price_min'] > 0) {
            $arguments['price_min'] = (float) $options['price_min'];
        }

        if (isset($options['price_max']) && (float) $options['price_max'] > 0) {
            $arguments['price_max'] = (float) $options['price_max'];
        }

        if (!empty($options['sort']) && in_array($options['sort'], ['relevance', 'price_asc', 'price_desc'], true)) {
            $arguments['sort'] = $options['sort'];
        }

        if (!empty($options['required_features']) && is_array($options['required_features'])) {
            $arguments['required_features'] = array_values($options['required_features']);
        }

        $arguments['limit'] = max(1, min(self::MAX_LIMIT, (int) ($options['limit'] ?? self::MAX_LIMIT)));

        // У BestPrice MCP есть лимит запросов (HTTP 429, код -32029): одинаковые поиски берём из кэша.
        $cacheKey = 'bestprice_search_v1_' . md5((string) json_encode($arguments, JSON_UNESCAPED_UNICODE));
        $cached = Cache::get($cacheKey);

        if (is_array($cached)) {
            return $this->result(true, $cached, null);
        }

        // После 429 некоторое время не обращаемся к MCP, чтобы лимит успел восстановиться.
        if (Cache::has(self::COOLDOWN_CACHE_KEY)) {
            return $this->result(false, [], 'BestPrice MCP rate limit (cooldown)', true);
        }

        try {
            $data = $this->callTool('search_products', $arguments);
            $products = $this->extractProducts($data);

            // Непустую выдачу кэшируем надолго, пустую ненадолго (вдруг временный сбой).
            Cache::put($cacheKey, $products, $products === [] ? 600 : $this->cacheTtl);

            return $this->result(true, $products, null);
        } catch (\Throwable $e) {
            if ($e->getCode() === 429) {
                Cache::put(self::COOLDOWN_CACHE_KEY, true, $this->cooldownSeconds);
                Log::warning('BestPrice MCP rate limit (429), cooldown ' . $this->cooldownSeconds . 's', ['query' => $query]);

                return $this->result(false, [], $e->getMessage(), true);
            }

            Log::error('BestPrice MCP search failed: ' . $e->getMessage(), [
                'query' => $query,
                'arguments' => $arguments,
            ]);

            return $this->result(false, [], $e->getMessage());
        }
    }

    /**
     * Список инструментов сервера (для отладки и сверки параметров search_products).
     */
    public function listTools(): array
    {
        $session = $this->session();

        $response = $this->post($session, [
            'jsonrpc' => '2.0',
            'method' => 'tools/list',
            'id' => 3,
        ]);

        $data = $this->decode($response, 3);

        return $data['result']['tools'] ?? [];
    }

    protected function callTool(string $name, array $arguments): array
    {
        $lastError = null;

        // Две попытки: если сессия протухла, сбрасываем её и инициализируемся заново.
        for ($attempt = 0; $attempt < 2; $attempt++) {
            $session = $this->session($attempt > 0);

            $response = $this->post($session, [
                'jsonrpc' => '2.0',
                'method' => 'tools/call',
                'params' => [
                    'name' => $name,
                    'arguments' => $arguments,
                ],
                'id' => 2,
            ], 25);

            // Лимит запросов: повторы и новая инициализация только ухудшат ситуацию.
            if ($response->status() === 429) {
                throw new \RuntimeException('MCP rate limit exceeded (HTTP 429)', 429);
            }

            if ($response->successful()) {
                $data = $this->decode($response, 2);

                if (is_array($data)) {
                    return $data;
                }

                $lastError = 'unparseable MCP response: ' . mb_substr($response->body(), 0, 300);
            } else {
                $lastError = 'HTTP ' . $response->status() . ': ' . mb_substr($response->body(), 0, 300);
            }

            Cache::forget(self::SESSION_CACHE_KEY);
        }

        throw new \RuntimeException($lastError ?? 'unknown MCP error');
    }

    protected function session(bool $fresh = false): array
    {
        if (!$fresh) {
            $cached = Cache::get(self::SESSION_CACHE_KEY);

            if (is_array($cached) && isset($cached['version'])) {
                return $cached;
            }
        }

        return $this->initialize();
    }

    protected function initialize(): array
    {
        $bootstrap = ['id' => null, 'version' => self::PROTOCOL_VERSION];

        $response = $this->post($bootstrap, [
            'jsonrpc' => '2.0',
            'method' => 'initialize',
            'params' => [
                'protocolVersion' => self::PROTOCOL_VERSION,
                // Должен сериализоваться как JSON-объект {}, а не массив [].
                'capabilities' => new \stdClass(),
                'clientInfo' => [
                    'name' => 'BuyerGreeceLaravelClient',
                    'version' => '1.1.0',
                ],
            ],
            'id' => 1,
        ], 10);

        if (!$response->successful()) {
            throw new \RuntimeException(
                'MCP initialize failed: HTTP ' . $response->status() . ' ' . mb_substr($response->body(), 0, 300),
                $response->status()
            );
        }

        $data = $this->decode($response, 1);

        if (!is_array($data) || isset($data['error'])) {
            throw new \RuntimeException(
                'MCP initialize error: ' . mb_substr(json_encode($data, JSON_UNESCAPED_UNICODE) ?: '', 0, 300)
            );
        }

        $session = [
            'id' => $response->header('Mcp-Session-Id') ?: null,
            'version' => $data['result']['protocolVersion'] ?? self::PROTOCOL_VERSION,
        ];

        // Обязательное уведомление после initialize.
        try {
            $this->post($session, [
                'jsonrpc' => '2.0',
                'method' => 'notifications/initialized',
            ], 10);
        } catch (\Throwable $e) {
            Log::warning('BestPrice MCP notifications/initialized failed: ' . $e->getMessage());
        }

        // Сервер без Mcp-Session-Id не хранит сессий: инициализация нужна редко, держим её дольше.
        Cache::put(self::SESSION_CACHE_KEY, $session, $session['id'] ? self::SESSION_TTL : 3600);

        return $session;
    }

    protected function post(array $session, array $payload, int $timeout = 20): Response
    {
        $headers = [
            'Accept' => 'application/json, text/event-stream',
            'Content-Type' => 'application/json',
            'MCP-Protocol-Version' => $session['version'],
        ];

        if (!empty($session['id'])) {
            $headers['Mcp-Session-Id'] = $session['id'];
        }

        return Http::timeout($timeout)->withHeaders($headers)->post($this->endpoint, $payload);
    }

    /**
     * Ответ может прийти как обычный JSON или как SSE (text/event-stream).
     */
    protected function decode(Response $response, int $id): ?array
    {
        $type = strtolower((string) $response->header('Content-Type'));

        if (str_contains($type, 'text/event-stream')) {
            $fallback = null;

            foreach (preg_split('/\r?\n/', $response->body()) as $line) {
                if (!str_starts_with($line, 'data:')) {
                    continue;
                }

                $json = json_decode(trim(substr($line, 5)), true);

                if (is_array($json) && (isset($json['result']) || isset($json['error']))) {
                    if (($json['id'] ?? null) === $id) {
                        return $json;
                    }

                    $fallback ??= $json;
                }
            }

            return $fallback;
        }

        $json = $response->json();

        return is_array($json) ? $json : null;
    }

    protected function extractProducts(array $data): array
    {
        if (isset($data['error'])) {
            throw new \RuntimeException(
                'MCP error: ' . json_encode($data['error'], JSON_UNESCAPED_UNICODE),
                ((int) ($data['error']['code'] ?? 0)) === -32029 ? 429 : 0
            );
        }

        $result = $data['result'] ?? [];

        if (!empty($result['isError'])) {
            $texts = [];
            foreach ((array) ($result['content'] ?? []) as $block) {
                if (is_array($block) && isset($block['text']) && is_string($block['text'])) {
                    $texts[] = $block['text'];
                }
            }

            throw new \RuntimeException('MCP tool error: ' . mb_substr(implode(' ', $texts), 0, 300));
        }

        if (isset($result['structuredContent']['products']) && is_array($result['structuredContent']['products'])) {
            return $result['structuredContent']['products'];
        }

        foreach ((array) ($result['content'] ?? []) as $block) {
            if (!is_array($block) || !isset($block['text']) || !is_string($block['text'])) {
                continue;
            }

            $text = $block['text'];

            $json = json_decode($text, true);
            if (is_array($json) && isset($json['products']) && is_array($json['products'])) {
                return $json['products'];
            }

            if (preg_match('/Structured result \(JSON\):\s*(\{.*\})/s', $text, $matches)) {
                $decoded = json_decode($matches[1], true);
                if (is_array($decoded) && isset($decoded['products']) && is_array($decoded['products'])) {
                    return $decoded['products'];
                }
            }
        }

        Log::warning('BestPrice MCP: unrecognised result shape', [
            'snippet' => mb_substr(json_encode($result, JSON_UNESCAPED_UNICODE) ?: '', 0, 500),
        ]);

        return [];
    }

    protected function result(bool $ok, array $products, ?string $error, bool $rateLimited = false): array
    {
        return ['ok' => $ok, 'products' => $products, 'error' => $error, 'rate_limited' => $rateLimited];
    }
}
