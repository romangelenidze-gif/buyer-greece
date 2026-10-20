<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use Auditable, HasFactory;

    protected $fillable = [
        'order_id',
        'product_name',
        'product_url',       // Исправлено с 'url' на 'product_url'
        'quantity',
        'size',              // Добавлено
        'color',             // Добавлено
        'model',             // Добавлено
        'options',
        'requested_comment', // Добавлено (сюда попадет комментарий клиента)
        'unit_price',
        'price_per_item',
        'total_price',
        'notes',
        'admin_comment',
        'screenshot_path',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getScreenshotUrlAttribute(): ?string
    {
        if (! $this->screenshot_path) {
            return null;
        }

        return route('orders.items.screenshot', [$this->order_id, $this->id]);
    }
}