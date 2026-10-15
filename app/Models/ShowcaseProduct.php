<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShowcaseProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'short_description',
        'image_path',
        'partner_name',
        'partner_product_url',
        'price',
        'old_price',
        'currency',
        'sort_order',
        'is_published',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'old_price' => 'decimal:2',
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Вычисление процента скидки для карточки
    public function getDiscountPercentAttribute(): ?int
    {
        if ($this->old_price && $this->price && $this->old_price > $this->price) {
            return (int) round((($this->old_price - $this->price) / $this->old_price) * 100);
        }
        return null;
    }
}