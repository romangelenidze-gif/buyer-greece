<?php

namespace App\Console\Commands;

use App\Models\FeedProduct;
use Illuminate\Console\Command;
use Meilisearch\Client;

class ConfigureMeilisearchCommand extends Command
{
    protected $signature = 'feeds:configure-search';

    protected $description = 'Настройка атрибутов, сортировки, distinct и мультиязычных синонимов в Meilisearch';

    public function handle(): int
    {
        $host = (string) config('scout.meilisearch.host');
        $key = (string) config('scout.meilisearch.key');

        if ($host === '') {
            $this->error('MEILISEARCH_HOST не задан в конфигурации.');

            return self::FAILURE;
        }

        $client = new Client($host, $key ?: null);
        $indexName = (new FeedProduct())->searchableAs();
        $index = $client->index($indexName);

        $this->info("Настройка индекса «{$indexName}»...");

        $index->updateDistinctAttribute('group_key');

        $index->updateSearchableAttributes([
            'title_ru',
            'title_ka',
            'title_en',
            'title_el',
            'brand',
            'model_code',
            'sku',
            'category_keywords',
            'color_keywords',
            'gender_keywords',
            'search_text',
        ]);

        $index->updateFilterableAttributes([
            'is_active',
            'in_stock',
            'feed',
            'brand',
            'gender',
            'category_top',
            'category_id',
            'price',
            'discount_percent',
        ]);

        $index->updateSortableAttributes([
            'price',
            'rating',
            'discount_percent',
            'id',
        ]);

        $index->updateSynonyms([
            'adidas' => ['адидас', 'ადიდასი', 'ადიდას'],
            'адидас' => ['adidas', 'ადიდასი'],
            'ადიდასი' => ['adidas', 'адидас'],
            'nike' => ['найк', 'найки', 'ნაიკი'],
            'найк' => ['nike', 'ნაიკი'],
            'ნაიკი' => ['nike', 'найк'],
            'кроссовки' => ['sneakers', 'shoes', 'running shoes', 'παπουτσια', 'ფეხსაცმელი', 'ბოტასები'],
            'sneakers' => ['кроссовки', 'shoes', 'παπουτσια', 'ფეხსაცმელი', 'ბოტასები'],
            'ფეხსაცმელი' => ['кроссовки', 'sneakers', 'shoes', 'παπουτσια', 'ბოტასები'],
            'ბოტასები' => ['кроссовки', 'sneakers', 'shoes', 'ფეხსაცმელი'],
            'куртка' => ['jacket', 'μπουφαν', 'ქურთუკი', 'ветровка', 'пуховик'],
            'jacket' => ['куртка', 'μπουφαν', 'ქურთუკი'],
            'ქურთუკი' => ['куртка', 'jacket', 'μπουφαν'],
            'худи' => ['hoodie', 'sweatshirt', 'φουτερ', 'ჰუდი', 'толстовка'],
            'hoodie' => ['худи', 'φουτερ', 'ჰუდი', 'толстовка'],
            'ჰუდი' => ['худи', 'hoodie', 'φουτερ'],
            'футболка' => ['t-shirt', 'tee', 'top', 'μπλουζα', 'მაისური'],
            'მაისური' => ['футболка', 't-shirt', 'μπλουζα'],
            'брюки' => ['pants', 'track pants', 'παντελονι', 'შარვალი', 'штаны'],
            'შარვალი' => ['брюки', 'pants', 'παντελονι'],
            'шорты' => ['shorts', 'σορτσ', 'შორტი'],
            'შორტი' => ['шорты', 'shorts', 'σορτσ'],
            'рюкзак' => ['backpack', 'σακιδιο', 'ზურგჩანთა'],
            'ზურგჩანთა' => ['рюкзак', 'backpack', 'σακιδιο'],
            'сандалии' => ['sandals', 'πεδιλα', 'σανδαλια', 'სანდლები', 'босоножки'],
            'სანდლები' => ['сандалии', 'sandals', 'πεδιλα'],
            'бутсы' => ['football boots', 'cleats', 'ποδοσφαιρικα', 'ბუცები'],
            'ბუცები' => ['бутсы', 'football boots', 'ποδοσφαιρικα'],
        ]);

        $this->info('Настройки индекса Meilisearch успешно применены!');

        return self::SUCCESS;
    }
}