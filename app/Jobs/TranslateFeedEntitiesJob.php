<?php

namespace App\Jobs;

use App\Models\EntityTranslation;
use App\Services\GroqAiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TranslateFeedEntitiesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 8;
    public int $backoff = 20;
    public int $timeout = 90;

    /**
     * @param string[] $items
     */
    public function __construct(
        public string $entityType,
        public array $items
    ) {
    }

    public function handle(GroqAiService $groq): void
    {
        $items = array_values(array_unique(array_filter(array_map('trim', $this->items))));
        if ($items === []) {
            return;
        }

        $translated = $groq->translateEntitiesBatch($items, $this->entityType);
        if ($translated === null) {
            if ($this->attempts() < $this->tries) {
                $this->release(20);
            }

            return;
        }

        $now = now();
        $rows = [];

        foreach ($items as $idx => $sourceText) {
            $tr = $translated[$idx] ?? null;
            if ($tr === null) {
                continue;
            }

            $hash = EntityTranslation::hash($sourceText);
            $rows[] = [
                'entity_type' => $this->entityType,
                'source_hash' => $hash,
                'source_text' => mb_substr($sourceText, 0, 500),
                'text_en' => $tr['en'],
                'text_ru' => $tr['ru'],
                'text_ka' => $tr['ka'],
                'created_at' => $now,
                'updated_at' => $now,
            ];

            foreach (['en', 'ru', 'ka'] as $loc) {
                Cache::forget("entity_tr:{$this->entityType}:{$loc}:{$hash}");
            }
        }

        if ($rows !== []) {
            DB::table('entity_translations')->upsert(
                $rows,
                ['entity_type', 'source_hash'],
                ['text_en', 'text_ru', 'text_ka', 'updated_at']
            );
        }

        // Пауза для соблюдения лимита Groq 8 000 TPM при параллельной работе воркеров
        if ($this->job !== null) {
            sleep(5);
        }
    }
}