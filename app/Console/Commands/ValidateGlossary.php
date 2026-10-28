<?php

namespace App\Console\Commands;

use App\Services\BestPriceMcpService;
use App\Services\LocalQueryAnalyzer;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Положить в app/Console/Commands/ValidateGlossary.php
 *
 * Прогоняет каждую запись config/search_glossary.php через BestPrice и показывает, где выдача пустая
 * (скорее всего неверное греческое название). Запуск:
 *
 *   php artisan search:validate-glossary                 # только основные названия категорий
 *   php artisan search:validate-glossary --genders       # плюс формы «мужской/женский/детский»
 *   php artisan search:validate-glossary --only=кроссовк # одна запись (по основе, el или en)
 */
class ValidateGlossary extends Command
{
    protected $signature = 'search:validate-glossary
        {--only= : Проверить только записи, где эта подстрока есть в основах или названиях}
        {--genders : Проверять и формы пола}
        {--min=1 : Минимум товаров в выдаче, чтобы считать запрос рабочим}
        {--sleep=1500 : Пауза между запросами, мс (у BestPrice есть лимит частоты)}';

    protected $description = 'Проверка греческих названий словаря поиска через BestPrice MCP';

    public function handle(): int
    {
        $rows = (array) config('search_glossary.categories', []);
        $analyzer = app(LocalQueryAnalyzer::class);
        $bestPrice = app(BestPriceMcpService::class);

        $expand = new \ReflectionMethod($analyzer, 'expandCategory');
        $expand->setAccessible(true);

        $only = mb_strtolower((string) $this->option('only'));
        $min = max(1, (int) $this->option('min'));
        $sleep = max(0, (int) $this->option('sleep'));
        // Успешные ответы кэшируются, поэтому повторный запуск команды пропускает уже проверенное.

        $checked = 0;
        $bad = [];

        foreach ($rows as $row) {
            $category = $expand->invoke($analyzer, (array) $row);

            if ($category === null) {
                continue;
            }

            $haystack = mb_strtolower(implode(' ', $category['stems']) . ' ' . $category['el'] . ' ' . $category['en']);

            if ($only !== '' && !str_contains($haystack, $only)) {
                continue;
            }

            $queries = [$category['el']];

            if ($this->option('genders')) {
                $queries = array_merge($queries, array_values($category['gender']));
            }

            foreach (array_unique($queries) as $query) {
                $result = $this->searchWithBackoff($bestPrice, $query);
                $count = count($result['products']);
                $checked++;

                if (!$result['ok']) {
                    $this->line('<fg=red>ERR </> ' . $query . ' | ' . Str::limit((string) $result['error'], 120));
                    $bad[] = [$category['stems'][0], $query, 'ошибка MCP'];
                } elseif ($count < $min) {
                    $this->line('<fg=red>FAIL</> ' . $query . ' (0 товаров)');
                    $bad[] = [$category['stems'][0], $query, '0 товаров'];
                } else {
                    $title = (string) ($result['products'][0]['title'] ?? '');
                    $this->line('<fg=green>OK  </> ' . $query . ' (' . $count . ') | ' . Str::limit($title, 70));
                }

                if ($sleep > 0) {
                    usleep($sleep * 1000);
                }
            }
        }

        $this->newLine();
        $this->info("Проверено запросов: {$checked}, проблемных: " . count($bad));

        if ($bad !== []) {
            $this->table(['Основа', 'Запрос', 'Проблема'], $bad);
            $this->line('Исправьте греческое название в config/search_glossary.php (или удалите строку: тогда запрос уйдёт в LLM).');
        }

        return self::SUCCESS;
    }

    /**
     * При лимите BestPrice (HTTP 429) ждём 30/60/90 секунд и повторяем тот же запрос.
     */
    protected function searchWithBackoff(BestPriceMcpService $bestPrice, string $query): array
    {
        $result = [];

        foreach ([0, 30, 60, 90] as $wait) {
            if ($wait > 0) {
                $this->line("<fg=yellow>лимит BestPrice, жду {$wait} с...</>");
                sleep($wait);
            }

            $result = $bestPrice->searchProducts($query, ['limit' => 3]);

            if (empty($result['rate_limited'])) {
                return $result;
            }
        }

        return $result;
    }
}
