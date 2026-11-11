<?php

namespace App\Console\Commands;

use App\Services\Feeds\AwinFeedImporter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportFeedCommand extends Command
{
    protected $signature = 'feeds:import {feed=adidas : Ключ фида из config/feeds.php} {--source= : URL или путь к локальному .csv / .csv.gz}';

    protected $description = 'Импорт партнёрского продуктового фида (Awin) в таблицу feed_products';

    public function handle(AwinFeedImporter $importer): int
    {
        $feed = (string) $this->argument('feed');
        $this->info("Импорт фида «{$feed}»...");

        try {
            $stats = $importer->import($feed, $this->option('source') ?: null);
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            Log::error('Feed import failed', ['feed' => $feed, 'error' => $e->getMessage()]);

            return self::FAILURE;
        }

        $this->table(
            ['Строк в фиде', 'Загружено', 'Пропущено', 'Деактивировано'],
            [[$stats['total'], $stats['imported'], $stats['skipped'], $stats['deactivated']]]
        );

        if ($stats['deactivation_skipped']) {
            $this->warn('Фид намного меньше текущей базы: пропавшие товары НЕ деактивированы. Проверьте фид.');
        }

        return self::SUCCESS;
    }
}
