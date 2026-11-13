<?php

namespace App\Console\Commands;

use App\Models\FeedProduct;
use App\Services\Feeds\AwinFeedImporter;
use App\Services\Feeds\FeedEntityLocalizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ImportFeedCommand extends Command
{
    protected $signature = 'feeds:import
        {feed=adidas : Ключ фида из config/feeds.php}
        {--source= : URL или путь к локальному .csv / .csv.gz}
        {--limit=0 : Импортировать только первые N товаров (для безопасного теста)}';

    protected $description = 'Импорт партнёрского продуктового фида в таблицу feed_products';

    public function handle(AwinFeedImporter $importer, FeedEntityLocalizer $localizer): int
    {
        $feed = (string) $this->argument('feed');
        $limit = max(0, (int) $this->option('limit'));

        $this->info($limit > 0
            ? "Тестовый импорт фида «{$feed}» (лимит: {$limit} товаров)..."
            : "Импорт фида «{$feed}»...");

        try {
            $stats = $importer->import($feed, $this->option('source') ?: null, $limit);
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            Log::error('Feed import failed', ['feed' => $feed, 'error' => $e->getMessage()]);

            return self::FAILURE;
        }

        Cache::forget("home.category_chips.v2.{$feed}");
        Cache::forget('autocomplete.brands.all.v1');

        $this->table(
            ['Строк прочитано', 'Загружено', 'Пропущено', 'Деактивировано'],
            [[$stats['total'], $stats['imported'], $stats['skipped'], $stats['deactivated']]]
        );

        if ($stats['deactivation_skipped']) {
            $this->warn('Деактивация отсутствующих товаров пропущена (режим теста или малый объём фида).');
        }

        $dispatched = $localizer->dispatchMissingTranslations($feed, false);
        if ($dispatched > 0) {
            $this->info("Поставлено в очередь на перевод новых сущностей: {$dispatched}.");
        }

        if (Schema::hasTable('autocomplete_entries')) {
            $this->call('autocomplete:rebuild');
        }

        if (config('scout.driver') === 'meilisearch') {
            $this->call('scout:import', ['model' => FeedProduct::class]);
        }

        return self::SUCCESS;
    }
}