<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncAutocompleteHits extends Command
{
    protected $signature = 'autocomplete:sync-hits';
    protected $description = 'Sync autocomplete click hits from Redis to MySQL safely';

    public function handle()
    {
        try {
            // Пример ключа счетчиков в Redis (замените на ваш актуальный ключ, если он отличается)
            $redisKey = 'autocomplete_hits_counter';
            
            // Проверяем существование данных в Redis перед обработкой
            if (Redis::exists($redisKey) === 0) {
                // Если данных нет, завершаем задачу успешно (код 0), не вызывая падений планировщика
                return self::SUCCESS;
            }

            $hits = Redis::hgetall($redisKey);

            if (empty($hits)) {
                return self::SUCCESS;
            }

            // Логика синхронизации с базой данных MySQL
            foreach ($hits as $query => $count) {
                if (empty($query)) {
                    continue;
                }

                DB::table('learned_autocomplete')
                    ->where('query', $query)
                    ->increment('hits', (int)$count);
            }

            // Очищаем обработанные данные в Redis
            Redis::del($redisKey);

            return self::SUCCESS;

        } catch (\Exception $e) {
            // Логируем ошибку, но возвращаем SUCCESS (0), чтобы крон-планировщик Laravel 
            // продолжал работать дальше и не сыпал критическими ошибками в лог.
            Log::warning('Autocomplete sync hits warning: ' . $e->getMessage());
            
            return self::SUCCESS;
        }
    }
}