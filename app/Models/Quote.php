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
    ];

    // Аксессоры для совпадения с именами в тесте
    public function getItemsTotalEurAttribute(): float
    {
        return (float) $this->product_total;
    }

    public function getCommissionEurAttribute(): float
    {
        return (float) $this->buyer_fee;
    }

    public function getShippingEurAttribute(): float
    {
        return (float) $this->local_shipping;
    }

    public function getTotalEurAttribute(): float
    {
        return (float) $this->total;
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