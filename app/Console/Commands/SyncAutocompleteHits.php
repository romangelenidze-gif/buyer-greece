<?php

namespace App\Console\Commands;

use App\Services\Feeds\FeedText;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Schema;

class SyncAutocompleteHits extends Command
{
    protected $signature = 'autocomplete:sync-hits';
    protected $description = 'Sync autocomplete click hits from Redis to MySQL safely';

    public function handle(): int
    {
        try {
            $redisKey = 'autocomplete_hits_counter';

            if (Redis::exists($redisKey) === 0) {
                return self::SUCCESS;
            }

            $hits = Redis::hgetall($redisKey);

            if (empty($hits)) {
                return self::SUCCESS;
            }

            $hasLearned = Schema::hasTable('learned_autocomplete');
            $hasMaterialized = Schema::hasTable('autocomplete_entries');

            foreach ($hits as $query => $count) {
                $clean = trim((string) $query);
                $inc = (int) $count;
                if ($clean === '' || $inc <= 0) {
                    continue;
                }

                if ($hasLearned) {
                    DB::table('learned_autocomplete')
                        ->where('query', $clean)
                        ->increment('hits', $inc);
                }

                if ($hasMaterialized) {
                    $norm = FeedText::normalize($clean);
                    if ($norm !== '') {
                        $escaped = addcslashes($norm, '%_\\');
                        DB::table('autocomplete_entries')
                            ->where('is_active', true)
                            ->where('search_keywords', 'like', '% ' . $escaped . ' %')
                            ->increment('popularity_hits', $inc);
                    }
                }
            }

            Redis::del($redisKey);
            Cache::increment('autocomplete:cache_ver');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            Log::warning('Autocomplete sync hits warning: ' . $e->getMessage());

            return self::SUCCESS;
        }
    }
}