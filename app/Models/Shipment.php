<?php

namespace App\Models;

use App\Enums\ShipmentStatus;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shipment extends Model
{
    use HasFactory, Auditable, SoftDeletes;

    protected $fillable = [
        'public_shipment_number',
        'customer_id',
        'status',
        'carrier',
        'destination_country',
        'weight_kg',
        'camex_tracking_number',
        'camex_status',
        'transferred_to_camex_at',
        'internal_note',
    ];

    protected $casts = [
        'status' => ShipmentStatus::class,
        'weight_kg' => 'decimal:2',
        'transferred_to_camex_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(IncomingPackage::class);
    }
}