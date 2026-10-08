<?php

namespace App\Actions\Shipments;

use App\Enums\OrderStatus;
use App\Enums\PackageStatus;
use App\Enums\ShipmentStatus;
use App\Models\Shipment;
use App\Notifications\ShipmentTransferredToCamexNotification;
use DomainException;
use Illuminate\Support\Facades\DB;

class TransferToCamexAction
{
    public function execute(Shipment $shipment, string $camexTrackingNumber, ?string $camexStatus = null, ?int $managerUserId = null): Shipment
    {
        return DB::transaction(function () use ($shipment, $camexTrackingNumber, $camexStatus, $managerUserId) {
            /** @var Shipment $lockedShipment */
            $lockedShipment = Shipment::where('id', $shipment->id)->lockForUpdate()->firstOrFail();

            if ($lockedShipment->transferred_to_camex_at !== null || $lockedShipment->status === ShipmentStatus::TRANSFERRED_TO_CAMEX) {
                throw new DomainException("Отправление #{$lockedShipment->public_shipment_number} уже передано в Camex.");
            }

            $lockedShipment->update([
                'camex_tracking_number' => $camexTrackingNumber,
                'camex_status' => $camexStatus,
                'transferred_to_camex_at' => now(),
                'status' => ShipmentStatus::TRANSFERRED_TO_CAMEX->value,
            ]);

            $lockedShipment->packages()->update([
                'status' => PackageStatus::ASSIGNED_TO_SHIPMENT->value,
            ]);

            foreach ($lockedShipment->packages as $package) {
                if ($package->order) {
                    $package->order->update([
                        'status' => OrderStatus::COMPLETED->value,
                        'completed_at' => now(),
                    ]);
                }
            }

            activity()
                ->performedOn($lockedShipment)
                ->causedBy($managerUserId ?? auth()->id())
                ->withProperties([
                    'camex_tracking_number' => $camexTrackingNumber,
                    'packages_count' => $lockedShipment->packages()->count(),
                ])
                ->log("Отправление #{$lockedShipment->public_shipment_number} передано в Camex (Трек: {$camexTrackingNumber})");

            DB::afterCommit(function () use ($lockedShipment) {
                if ($lockedShipment->customer) {
                    $lockedShipment->customer->notify(new ShipmentTransferredToCamexNotification($lockedShipment));
                }
            });

            return $lockedShipment;
        });
    }
}