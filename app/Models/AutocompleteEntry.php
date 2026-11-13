<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutocompleteEntry extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'price' => 'float',
            'products_count' => 'integer',
            'popularity_hits' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function titleFor(string $locale): string
    {
        return match ($locale) {
            'ru' => $this->title_ru ?: $this->title_en,
            'ka' => $this->title_ka ?: $this->title_en,
            'el' => $this->title_el ?: $this->title_en,
            default => $this->title_en,
        };
    }

    public function badgeFor(string $locale): string
    {
        return match ($locale) {
            'ru' => (string) ($this->badge_ru ?: $this->badge_en),
            'ka' => (string) ($this->badge_ka ?: $this->badge_en),
            'el' => (string) ($this->badge_el ?: $this->badge_en),
            default => (string) $this->badge_en,
        };
    }
}