<?php

namespace App\Console\Commands;

use App\Jobs\TranslateFeedEntitiesJob;
use App\Models\EntityTranslation;
use App\Services\Feeds\FeedEntityLocalizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SyncFeedTaxonomyCommand extends Command
{
    protected $signature = 'feeds:sync-taxonomy
        {feed? : Ключ фида (например adidas) или пусто для всех}
        {--sync : Выполнить перевод сразу в текущем процессе через Groq}
        {--limit=0 : Ограничить максимальное число переводимых строк}
        {--export : Выгрузить компактную выжимку частями по 250 строк в storage/app/export/}
        {--chunk=250 : Размер одного файла при экспорте}
        {--min-prefix=3 : Мин. встречаемость префикса названия в товарах}
        {--min-color=4 : Мин. встречаемость цвета в товарах}
        {--min-spec=5 : Мин. встречаемость характеристики в товарах}
        {--import= : Путь к переведённому JSON-файлу или папке (например storage/app/translated)}';

    protected $description = 'Экспорт компактной выжимки частями для бесплатного Gemini и импорт переводов';

    public function handle(FeedEntityLocalizer $localizer): int
    {
        $feed = $this->argument('feed') ? (string) $this->argument('feed') : null;

        if ($importPath = $this->option('import')) {
            return $this->importPath((string) $importPath);
        }

        $minCounts = [
            'category_segment' => 1,
            'title_prefix' => max(1, (int) $this->option('min-prefix')),
            'color' => max(1, (int) $this->option('min-color')),
            'spec' => max(1, (int) $this->option('min-spec')),
        ];

        $this->info('Сканирование базы и сбор частотной выжимки...');
        $missing = $localizer->collectUntranslatedEntities($feed, $minCounts);

        if ($missing === []) {
            $this->info('Все ключевые категории, цвета, префиксы и характеристики уже переведены!');

            return self::SUCCESS;
        }

        if ($this->option('export')) {
            $exportDir = storage_path('app/export');
            if (!is_dir($exportDir)) {
                mkdir($exportDir, 0775, true);
            }
            foreach (glob($exportDir . '/part_*.json') ?: [] as $oldFile) {
                @unlink($oldFile);
            }

            $summary = [];
            $flatList = [];

            foreach ($missing as $type => $items) {
                $summary[] = [$type, count($items)];
                foreach ($items as $src) {
                    $flatList[] = ['type' => $type, 'src' => $src];
                }
            }

            $chunkSize = max(50, (int) $this->option('chunk'));
            $chunks = array_chunk($flatList, $chunkSize);

            foreach ($chunks as $idx => $chunk) {
                $num = $idx + 1;
                file_put_contents(
                    $exportDir . "/part_{$num}.json",
                    json_encode($chunk, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                );
            }

            $this->table(['Тип сущности', 'Уникальных строк'], $summary);
            $this->info('Всего строк: ' . count($flatList) . '. Создано файлов в storage/app/export/: ' . count($chunks));
            $this->line('Загружай part_1.json, part_2.json... по очереди в бесплатный Gemini, а ответы сохраняй в storage/app/translated/');

            return self::SUCCESS;
        }

        $sync = (bool) $this->option('sync');
        $limit = max(0, (int) $this->option('limit'));

        $summary = [];
        $allBatches = [];
        $totalCount = 0;

        foreach ($missing as $type => $items) {
            if ($limit > 0) {
                $remaining = max(0, $limit - $totalCount);
                $items = array_slice($items, 0, $remaining);
            }
            if ($items === []) {
                continue;
            }

            $summary[] = [$type, count($items)];
            $totalCount += count($items);

            foreach (array_chunk($items, 20) as $chunk) {
                $allBatches[] = ['type' => $type, 'items' => $chunk];
            }
        }

        $this->table(['Тип сущности', 'Новых уникальных строк'], $summary);

        if (!$sync) {
            foreach ($allBatches as $batch) {
                TranslateFeedEntitiesJob::dispatch($batch['type'], $batch['items']);
            }
            $this->info('Отправлено в фоновую очередь задач: ' . count($allBatches) . " (строк: {$totalCount}).");

            return self::SUCCESS;
        }

        $this->info('Выполняем пакетный перевод через Groq...');
        $bar = $this->output->createProgressBar(count($allBatches));
        $bar->start();

        foreach ($allBatches as $batch) {
            TranslateFeedEntitiesJob::dispatchSync($batch['type'], $batch['items']);
            $bar->advance();
            usleep(350000);
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Перевод успешно завершён!');

        return self::SUCCESS;
    }

    private function importPath(string $path): int
    {
        $resolved = is_file($path) || is_dir($path) ? $path : base_path($path);

        if (!is_file($resolved) && !is_dir($resolved)) {
            $this->error("Путь не найден: {$path}");

            return self::FAILURE;
        }

        $files = is_dir($resolved)
            ? (glob(rtrim($resolved, '/\\') . '/*.json') ?: [])
            : [$resolved];

        if ($files === []) {
            $this->error("В папке нет JSON-файлов: {$resolved}");

            return self::FAILURE;
        }

        $now = now();
        $rows = [];
        $imported = 0;

        foreach ($files as $file) {
            $raw = (string) file_get_contents($file);
            $raw = (string) preg_replace('/^```(?:json)?\s*([\s\S]*?)\s*```$/u', '$1', trim($raw));

            $data = json_decode($raw, true);
            if (!is_array($data)) {
                $this->warn('Пропущен невалидный JSON: ' . basename($file));
                continue;
            }

            // Формат A: плоский список [{"type": "...", "src": "...", "en": "...", "ru": "...", "ka": "..."}]
            if (array_is_list($data)) {
                foreach ($data as $row) {
                    if (!is_array($row)) {
                        continue;
                    }
                    $entityType = trim((string) ($row['type'] ?? ''));
                    $src = trim((string) ($row['src'] ?? $row['source_text'] ?? ''));
                    if ($entityType === '' || $src === '') {
                        continue;
                    }
                    $this->queueUpsertRow($rows, $entityType, $src, $row, $now);
                }
            } else {
                // Формат B: сгруппированный по типу {"title_prefix": [{"src": "...", ...}]}
                foreach ($data as $entityType => $items) {
                    if (!is_array($items)) {
                        continue;
                    }
                    foreach ($items as $key => $row) {
                        if (!is_array($row)) {
                            continue;
                        }
                        $src = trim((string) ($row['src'] ?? $row['source_text'] ?? (is_string($key) ? $key : '')));
                        if ($src === '') {
                            continue;
                        }
                        $this->queueUpsertRow($rows, (string) $entityType, $src, $row, $now);
                    }
                }
            }

            if (count($rows) >= 500) {
                $imported += $this->flushRows($rows);
                $rows = [];
            }
        }

        $imported += $this->flushRows($rows);
        $this->info("Успешно загружено переводов в базу: {$imported} (из файлов: " . count($files) . ')');

        return self::SUCCESS;
    }

    private function queueUpsertRow(array &$rows, string $entityType, string $src, array $row, $now): void
    {
        $hash = EntityTranslation::hash($src);
        $en = trim((string) ($row['en'] ?? $src));
        $ru = trim((string) ($row['ru'] ?? $en));
        $ka = trim((string) ($row['ka'] ?? $en));

        $rows[$entityType . ':' . $hash] = [
            'entity_type' => $entityType,
            'source_hash' => $hash,
            'source_text' => mb_substr($src, 0, 500),
            'text_en' => mb_substr($en, 0, 500),
            'text_ru' => mb_substr($ru, 0, 500),
            'text_ka' => mb_substr($ka, 0, 500),
            'created_at' => $now,
            'updated_at' => $now,
        ];

        foreach (['en', 'ru', 'ka'] as $loc) {
            Cache::forget("entity_tr:{$entityType}:{$loc}:{$hash}");
        }
    }

    private function flushRows(array $rows): int
    {
        if ($rows === []) {
            return 0;
        }

        DB::table('entity_translations')->upsert(
            array_values($rows),
            ['entity_type', 'source_hash'],
            ['text_en', 'text_ru', 'text_ka', 'updated_at']
        );

        return count($rows);
    }
}