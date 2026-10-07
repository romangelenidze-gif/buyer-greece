<?php

namespace App\Models;

use App\Enums\PackageSourceType;
use App\Enums\PackageStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'order_id',
        'shipment_id',
        'source_type',
        'status',
        'incoming_tracking_number',
        'store_name',
        'description',
        'weight_kg',
        'dimensions',
        'declared_value',
        'received_at',
        'notes',
    ];

    protected $casts = [
        'status' => PackageStatus::class,
        'source_type' => PackageSourceType::class,
        'weight_kg' => 'decimal:2',
        'declared_value' => 'decimal:2',
        'received_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }
}