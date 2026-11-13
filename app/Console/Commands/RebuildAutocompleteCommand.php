<?php

namespace App\Console\Commands;

use App\Services\Autocomplete\AutocompleteBuilder;
use Illuminate\Console\Command;

class RebuildAutocompleteCommand extends Command
{
    protected $signature = 'autocomplete:rebuild';

    protected $description = 'Пересборка материализованной таблицы подсказок autocomplete_entries из актуальных фидов';

    public function handle(AutocompleteBuilder $builder): int
    {
        $this->info('Пересборка индекса автокомплита...');
        $start = microtime(true);

        $stats = $builder->rebuild();
        $ms = round((microtime(true) - $start) * 1000);

        $this->table(
            ['Бренды', 'Категории', 'Фасеты (Бренд+Кат.)', 'Модели', 'Всего записей', 'Время'],
            [[$stats['brands'], $stats['categories'], $stats['facets'], $stats['models'], $stats['total'], "{$ms} мс"]]
        );

        return self::SUCCESS;
    }
}