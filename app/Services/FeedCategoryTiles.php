<?php

namespace App\Services;

use App\Models\FeedProduct;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

/**
 * Категории из config/home_categories.php: плашки для главной (название и число товаров)
 * и выборка товаров для страницы категории. Обе части используют один и тот же отбор,
 * поэтому число на плашке всегда совпадает с числом товаров на странице.
 */
class FeedCategoryTiles
{
    private const CACHE_MINUTES = 60;

    /**
     * Плашки для главной.
     *
     * @return Collection<int, array{slug: string, label: string, count: int, url: string}>
     */
    public function get(): Collection
    {
        $feed = $this->feed();

        // В кэше лежат «сырые» данные; перевод названий делаем на лету, потому что язык у посетителей разный.
        $raw = Cache::remember(
            "home.category_chips.v2.{$feed}",
            now()->addMinutes(self::CACHE_MINUTES),
            fn () => $this->build()
        );

        return collect($raw)->map(fn (array $tile) => [
            'slug' => $tile['slug'],
            'label' => __($tile['label']),
            'count' => $tile['count'],
            'url' => route('feed-category.show', ['slug' => $tile['slug']]),
        ]);
    }

    /** Описание категории по адресу или null, если такой нет. */
    public function tile(string $slug): ?array
    {
        foreach ((array) config('home_categories.tiles', []) as $tile) {
            if (($tile['slug'] ?? null) === $slug) {
                return $tile;
            }
        }

        return null;
    }

    /** Товары категории, доступные в наличии (без сортировки и пагинации). */
    public function productsQuery(array $tile): Builder
    {
        $query = FeedProduct::query()
            ->where('feed', $this->feed())
            ->where('is_active', true)
            ->where('in_stock', true);

        $this->applyFilter($query, (array) ($tile['where'] ?? []));

        return $query;
    }

    private function feed(): string
    {
        return (string) config('home_categories.feed', 'adidas');
    }

    private function build(): array
    {
        $tiles = [];

        foreach ((array) config('home_categories.tiles', []) as $tile) {
            $count = $this->productsQuery($tile)->count();
            if ($count === 0) {
                continue;
            }

            $tiles[] = [
                'slug' => (string) $tile['slug'],
                'label' => (string) $tile['label'],
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
