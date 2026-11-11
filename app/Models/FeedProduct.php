<?php

namespace App\Models;

use App\Services\Feeds\AwinLink;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Товар из партнёрского фида (одна расцветка модели). Таблица заполняется командой feeds:import.
 */
class FeedProduct extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'price' => 'float',
            'old_price' => 'float',
            'rating' => 'float',
            'delivery_cost' => 'float',
            'discount_percent' => 'integer',
            'in_stock' => 'boolean',
            'is_active' => 'boolean',
            'sizes' => 'array',
            'specs' => 'array',
            'last_seen_at' => 'datetime',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Ссылка на магазин с вашим партнёрским отслеживанием (английская страница, если включена в config/feeds.php).
     */
    public function outboundUrl(): string
    {
        return AwinLink::forProduct(
            $this->deep_link,
            $this->merchant_url,
            (array) config("feeds.feeds.{$this->feed}.english_url", [])
        );
    }

    /**
     * Единый вид карточки товара. Из этого массива рисуется карточка и на главной, и в поисковой выдаче,
     * поэтому набор полей везде одинаковый.
     */
    public function toCard(): array
    {
        return [
            'title' => $this->name,
            'brand' => $this->brand,
            'colour' => $this->colour,
            'image_url' => $this->image_url,
            'price' => $this->price,
            'price_amount' => $this->price,
            'old_price' => $this->old_price,
            'discount_percent' => $this->discount_percent,
            'currency' => $this->currency ?: 'EUR',
            'rating' => $this->rating,
            'in_stock' => $this->in_stock,
            'detail_url' => $this->detailUrl(),
            'product_url' => $this->outboundUrl(),
            'merchant_name' => (string) config("feeds.feeds.{$this->feed}.store_name", $this->feed),
            'source' => 'feed',
        ];
    }

    public function detailUrl(): string
    {
        return route('feed-product.show', ['feed' => $this->feed, 'sku' => $this->sku]);
    }
}
