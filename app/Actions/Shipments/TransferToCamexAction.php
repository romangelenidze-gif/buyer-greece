<?php

namespace App\Actions\Shipments;

use App\Enums\OrderStatus;
use App\Enums\PackageStatus;
use App\Enums\ShipmentStatus;
use App\Models\Shipment;
use App\Notifications\ShipmentTransferredToCamexNotification;
use Illuminate\Support\Facades\DB;

class TransferToCamexAction
{
    public function execute(Shipment $shipment, string $camexTrackingNumber, ?string $camexStatus = null, ?int $managerUserId = null): Shipment
    {
        return DB::transaction(function () use ($shipment, $camexTrackingNumber, $camexStatus, $managerUserId) {
            $shipment->update([
                'camex_tracking_number' => $camexTrackingNumber,
                'camex_status' => $camexStatus,
                'transferred_to_camex_at' => now(),
                'status' => ShipmentStatus::TRANSFERRED_TO_CAMEX->value,
            ]);

            $shipment->packages()->update([
                'status' => PackageStatus::ASSIGNED_TO_SHIPMENT->value,
            ]);

            foreach ($shipment->packages as $package) {
                if ($package->order) {
                    $package->order->update([
                        'status' => OrderStatus::COMPLETED->value,
                        'completed_at' => now(),
                    ]);
                }
            }

            activity()
                ->performedOn($shipment)
                ->causedBy($managerUserId ?? auth()->id())
                ->withProperties([
                    'camex_tracking_number' => $camexTrackingNumber,
                    'packages_count' => $shipment->packages()->count(),
                ])
                ->log("Отправление #{$shipment->public_shipment_number} передано в Camex (Трек: {$camexTrackingNumber})");

            DB::afterCommit(function () use ($shipment) {
                if ($shipment->customer) {
                    $shipment->customer->notify(new ShipmentTransferredToCamexNotification($shipment));
                }
            });

            return $shipment;
        });
    }
}