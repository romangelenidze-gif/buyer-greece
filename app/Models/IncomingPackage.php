<?php

namespace App\Models;

use App\Enums\PackageSourceType;
use App\Traits\Auditable;
use App\Enums\PackageStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncomingPackage extends Model
{
    use HasFactory, Auditable, SoftDeletes;

    protected $fillable = [
        'public_package_number',
        'customer_id',
        'order_id',
        'shipment_id',
        'source_type',
        'store_name',
        'store_tracking_number',
        'description',
        'expected_date',
        'received_at',
        'weight_kg',
        'dimensions',
        'status',
        'photos_path',
        'internal_note',
    ];

    protected $casts = [
        'source_type' => PackageSourceType::class,
        'status' => PackageStatus::class,
        'expected_date' => 'date',
        'received_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
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