<?php

namespace App\Console\Commands;

use App\Models\FeedProduct;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Meilisearch\Client;

class PurgeFeedCommand extends Command
{
    protected $signature = 'feeds:purge
        {feed : Ключ магазина из config/feeds.php}
        {--force : Выполнить без подтверждения}';

    protected $description = 'Полностью удаляет товары указанного магазина из БД, Meilisearch, автокомплита и кэша';

    public function handle(): int
    {
        $feed = trim((string) $this->argument('feed'));
        if ($feed === '') {
            $this->error('Укажите ключ магазина.');

            return self::FAILURE;
        }

        $count = DB::table('feed_products')->where('feed', $feed)->count();

        if (!$this->option('force') && !$this->confirm("Удалить все товары магазина «{$feed}» ({$count} шт.) из БД, поиска и автокомплита?")) {
            $this->info('Операция отменена.');

            return self::SUCCESS;
        }

        $deleted = DB::table('feed_products')->where('feed', $feed)->delete();
        $this->info("Удалено из feed_products: {$deleted}.");

        if (config('scout.driver') === 'meilisearch' && !empty(config('scout.meilisearch.host'))) {
            try {
                $client = new Client(
                    (string) config('scout.meilisearch.host'),
                    config('scout.meilisearch.key') ?: null
                );
                $indexName = (new FeedProduct())->searchableAs();
                $client->index($indexName)->deleteDocuments([
                    'filter' => 'feed = "' . addslashes($feed) . '"',
                ]);
                $this->info("Документы магазина «{$feed}» удалены из индекса Meilisearch.");
            } catch (\Throwable $e) {
                $this->warn('Не удалось очистить Meilisearch по фильтру: ' . $e->getMessage());
            }
        }

        if (Schema::hasTable('autocomplete_entries')) {
            $this->call('autocomplete:rebuild');
        }

        Cache::forget("home.category_chips.v2.{$feed}");
        Cache::forget('autocomplete.brands.all.v1');
        $this->call('cache:clear');

        $this->info("Магазин «{$feed}» полностью очищен без влияния на остальные каталоги.");

        return self::SUCCESS;
    }
}