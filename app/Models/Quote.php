<?php

namespace App\Models;

use App\Enums\QuoteStatus;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use HasFactory, Auditable, SoftDeletes;

    protected $fillable = [
        'order_id',
        'created_by_user_id',
        'status',
        'product_total',
        'local_shipping',
        'buyer_fee',
        'services_total',
        'other_costs',
        'discount',
        'total',
        'currency',
        'valid_until',
        'accepted_at',
        'rejected_at',
        'notes',
    ];

    protected $casts = [
        'status' => QuoteStatus::class,
        'valid_until' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
        'product_total' => 'float',
        'local_shipping' => 'float',
        'buyer_fee' => 'float',
        'services_total' => 'float',
        'other_costs' => 'float',
        'discount' => 'float',
        'total' => 'float',
    ];

    protected static function booted(): void
    {
        static::creating(function (Quote $quote) {
            if (auth()->check() && empty($quote->created_by_user_id)) {
                $quote->created_by_user_id = auth()->id();
            }
        });

        // Строгое вычисление итоговой суммы на уровне модели перед сохранением
        static::saving(function (Quote $quote) {
            $quote->total = ($quote->product_total + $quote->local_shipping + $quote->buyer_fee + $quote->services_total + $quote->other_costs) - $quote->discount;
        });

        // Автоматическая установка активного расчета для заказа
        static::saved(function (Quote $quote) {
            if ($quote->order) {
                $quote->order->updateQuietly(['active_quote_id' => $quote->id]);
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}