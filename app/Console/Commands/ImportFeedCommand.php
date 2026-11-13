<?php

namespace App\Console\Commands;

use App\Services\Feeds\AwinFeedImporter;
use App\Services\Feeds\FeedEntityLocalizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ImportFeedCommand extends Command
{
    protected $signature = 'feeds:import {feed=adidas : Ключ фида из config/feeds.php} {--source= : URL или путь к локальному .csv / .csv.gz}';

    protected $description = 'Импорт партнёрского продуктового фида (Awin) в таблицу feed_products';

    public function handle(AwinFeedImporter $importer, FeedEntityLocalizer $localizer): int
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

        Cache::forget("home.category_chips.v2.{$feed}");
        Cache::forget('autocomplete.brands.all.v1');

        $this->table(
            ['Строк в фиде', 'Загружено', 'Пропущено', 'Деактивировано'],
            [[$stats['total'], $stats['imported'], $stats['skipped'], $stats['deactivated']]]
        );

        if ($stats['deactivation_skipped']) {
            $this->warn('Фид намного меньше текущей базы: пропавшие товары НЕ деактивированы. Проверьте фид.');
        }

        $dispatched = $localizer->dispatchMissingTranslations($feed, false);
        if ($dispatched > 0) {
            $this->info("Поставлено в очередь на перевод новых сущностей: {$dispatched}.");
        }

        if (Schema::hasTable('autocomplete_entries')) {
            $this->call('autocomplete:rebuild');
        }

        return self::SUCCESS;
    }
}