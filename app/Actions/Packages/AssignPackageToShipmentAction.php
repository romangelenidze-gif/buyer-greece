<?php

namespace App\Actions\Packages;

use App\Enums\PackageStatus;
use App\Models\IncomingPackage;
use App\Models\Shipment;
use Illuminate\Support\Facades\DB;

class AssignPackageToShipmentAction
{
    /**
     * Привязка входящей посылки к консолидированной отгрузке.
     */
    public function execute(IncomingPackage $package, Shipment $shipment): IncomingPackage
    {
        return DB::transaction(function () use ($package, $shipment) {
            $package->update([
                'shipment_id' => $shipment->id,
                'status' => PackageStatus::ASSIGNED_TO_SHIPMENT,
            ]);

            return $package;
        });
    }
}