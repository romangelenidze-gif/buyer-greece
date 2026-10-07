<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'public_order_number',
        'customer_id',
        'type',
        'status',
        'active_quote_id',
        'internal_note',
        'submitted_at',
        'completed_at',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'type' => OrderType::class,
        'submitted_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->public_order_number)) {
                $order->public_order_number = 'ORD-' . strtoupper(Str::random(8));
            }

            if (empty($order->submitted_at)) {
                $order->submitted_at = now();
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function activeQuote(): BelongsTo
    {
        return $this->belongsTo(Quote::class, 'active_quote_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function incomingPackages(): HasMany
    {
        return $this->hasMany(IncomingPackage::class);
    }
}