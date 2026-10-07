<?php

namespace App\Models;

use App\Enums\ShipmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'public_shipment_number',
        'customer_id',
        'status',
        'weight_kg',
        'camex_tracking_number',
        'camex_status',
        'transferred_to_camex_at',
        'internal_note',
    ];

    protected $casts = [
        'status' => ShipmentStatus::class,
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