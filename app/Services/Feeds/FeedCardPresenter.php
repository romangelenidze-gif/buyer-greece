<?php

namespace App\Services\Feeds;

use App\Models\FeedProduct;
use App\Services\GroqAiService;
use Illuminate\Support\Collection;

/**
 * Готовит товары из базы для карточек: те же поля и тот же перевод названий, что в поисковой выдаче.
 * Используйте на главной и везде, где карточки выводятся не из поиска.
 */
class FeedCardPresenter
{
    public function __construct(protected GroqAiService $groq)
    {
    }

    /**
     * @param iterable<FeedProduct|array> $products
     */
    public function make(iterable $products, ?string $lang = null): Collection
    {
        $cards = collect($products)
            ->map(fn ($p) => $p instanceof FeedProduct ? $p->toCard() : (array) $p)
            ->values();

        if ($cards->isEmpty()) {
            return $cards;
        }

        // Названия переводятся пачкой и берутся из того же кэша (30 дней), что и в поиске.
        $translated = $this->groq->translateProductsBack($cards->pluck('title')->all(), $lang ?? app()->getLocale());

        return $cards->map(function (array $card, int $i) use ($translated) {
            $card['title'] = $translated[$i] ?? $card['title'];

            return $card;
        });
    }
}
