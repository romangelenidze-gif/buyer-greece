<?php

namespace App\Services;

use App\Models\FeedProduct;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

/**
 * Категории для главной: название и число товаров в наличии.
 * Список задаётся в config/home_categories.php; категории без товаров пропускаются.
 */
class FeedCategoryTiles
{
    private const CACHE_MINUTES = 60;

    /**
     * @return Collection<int, array{label: string, count: int, url: string}>
     */
    public function get(): Collection
    {
        $feed = (string) config('home_categories.feed', 'adidas');

        // В кэше лежат «сырые» данные; перевод названий делаем на лету, потому что язык у посетителей разный.
        $raw = Cache::remember(
            "home.category_chips.{$feed}",
            now()->addMinutes(self::CACHE_MINUTES),
            fn () => $this->build($feed)
        );

        return collect($raw)->map(fn (array $tile) => [
            'label' => __($tile['label']),
            'count' => $tile['count'],
            'url' => route('product-search', ['q' => $tile['q']]),
        ]);
    }

    private function build(string $feed): array
    {
        $tiles = [];

        foreach ((array) config('home_categories.tiles', []) as $tile) {
            $query = FeedProduct::query()
                ->where('feed', $feed)
                ->where('is_active', true)
                ->where('in_stock', true);

            $this->applyFilter($query, (array) ($tile['where'] ?? []));

            $count = $query->count();
            if ($count === 0) {
                continue;
            }

            $tiles[] = [
                'label' => (string) $tile['label'],
                'q' => (string) $tile['q'],
                'count' => $count,
            ];
        }

        return $tiles;
    }

    private function applyFilter(Builder $query, array $where): void
    {
        foreach ($where as $key => $value) {
            match ($key) {
                'category_top', 'gender' => $query->where($key, $value),
                'category_path' => $this->wherePathLike($query, (array) $value),
                'discount' => $value ? $query->where('discount_percent', '>', 0) : null,
                default => throw new InvalidArgumentException("config/home_categories.php: неизвестный ключ where «{$key}»"),
            };
        }
    }

    /** Совпадение category_path с любой из подстрок. */
    private function wherePathLike(Builder $query, array $needles): void
    {
        $query->where(function (Builder $q) use ($needles) {
            foreach ($needles as $needle) {
                $q->orWhere('category_path', 'like', '%' . $needle . '%');
            }
        });
    }
}
