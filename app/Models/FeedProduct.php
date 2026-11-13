<?php

namespace App\Models;

use App\Services\Feeds\AwinLink;
use App\Services\Feeds\FeedEntityLocalizer;
use App\Services\Feeds\FeedText;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class FeedProduct extends Model
{
    use Searchable;

    protected $guarded = [];

    /** @var array<string, array<string, EntityTranslation>>|null */
    private static ?array $translationCache = null;

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

    public function searchableAs(): string
    {
        return config('scout.prefix', '') . 'feed_products';
    }

    public function shouldBeSearchable(): bool
    {
        return (bool) $this->is_active;
    }

    public function toSearchableArray(): array
    {
        $dict = self::getTranslationDictionary();

        $rawName = trim((string) $this->name);
        $titleEl = $rawName;
        $titleEn = $rawName;
        $titleRu = $rawName;
        $titleKa = $rawName;

        $split = FeedEntityLocalizer::splitGreekTitle($rawName);
        if ($split !== null) {
            $h = EntityTranslation::hash($split['prefix']);
            if (isset($dict['title_prefix'][$h])) {
                $tr = $dict['title_prefix'][$h];
                $titleEn = trim($tr->forLocale('en') . ' ' . $split['model']);
                $titleRu = trim($tr->forLocale('ru') . ' ' . $split['model']);
                $titleKa = trim($tr->forLocale('ka') . ' ' . $split['model']);
            }
        }

        $catKeywords = [];
        foreach (explode(',', (string) $this->category_path) as $seg) {
            $seg = trim($seg);
            if ($seg === '') {
                continue;
            }
            $catKeywords[] = $seg;
            $h = EntityTranslation::hash($seg);
            if (isset($dict['category_segment'][$h])) {
                $tr = $dict['category_segment'][$h];
                $catKeywords[] = $tr->forLocale('en');
                $catKeywords[] = $tr->forLocale('ru');
                $catKeywords[] = $tr->forLocale('ka');
            }
        }

        $colorKeywords = [];
        foreach (explode('/', (string) $this->colour) as $colPart) {
            $colPart = trim($colPart);
            if ($colPart === '') {
                continue;
            }
            $colorKeywords[] = $colPart;
            $h = EntityTranslation::hash($colPart);
            if (isset($dict['color'][$h])) {
                $tr = $dict['color'][$h];
                $colorKeywords[] = $tr->forLocale('ru');
                $colorKeywords[] = $tr->forLocale('ka');
            }
        }

        $genderKeywords = match ($this->gender) {
            'men' => 'men мужские мужская мужской для мужчин მამაკაცის ανδρικα',
            'women' => 'women женские женская женский для женщин ქალის γυναικεια',
            'kids' => 'kids детские детская детский для детей საბავშვო παιδικα',
            'unisex' => 'unisex унисекс უნისექსი',
            default => '',
        };

        $groupKey = $this->feed . ':' . ($this->model_code ?: $this->sku);

        return [
            'id' => (int) $this->id,
            'feed' => (string) $this->feed,
            'sku' => (string) $this->sku,
            'model_code' => (string) ($this->model_code ?? ''),
            'group_key' => $groupKey,
            'brand' => (string) ($this->brand ?? ''),
            'title_el' => $titleEl,
            'title_en' => $titleEn,
            'title_ru' => $titleRu,
            'title_ka' => $titleKa,
            'category_keywords' => implode(' ', array_unique($catKeywords)),
            'color_keywords' => implode(' ', array_unique($colorKeywords)),
            'gender_keywords' => $genderKeywords,
            'search_text' => FeedText::normalize((string) $this->search_text),
            'category_top' => (string) ($this->category_top ?? ''),
            'category_path' => (string) ($this->category_path ?? ''),
            'category_id' => $this->category_id ? (int) $this->category_id : null,
            'gender' => (string) ($this->gender ?? 'unisex'),
            'price' => (float) $this->price,
            'discount_percent' => (int) ($this->discount_percent ?? 0),
            'rating' => $this->rating !== null ? (float) $this->rating : -1.0,
            'in_stock' => (bool) $this->in_stock,
            'is_active' => (bool) $this->is_active,
        ];
    }

    private static function getTranslationDictionary(): array
    {
        if (self::$translationCache !== null) {
            return self::$translationCache;
        }

        self::$translationCache = [
            'title_prefix' => [],
            'category_segment' => [],
            'color' => [],
        ];

        EntityTranslation::query()
            ->whereIn('entity_type', ['title_prefix', 'category_segment', 'color'])
            ->get()
            ->each(function (EntityTranslation $row) {
                self::$translationCache[$row->entity_type][$row->source_hash] = $row;
            });

        return self::$translationCache;
    }

    public function outboundUrl(): string
    {
        return AwinLink::forProduct(
            $this->deep_link,
            $this->merchant_url,
            (array) config("feeds.feeds.{$this->feed}.english_url", [])
        );
    }

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