<?php

namespace App\Actions\Packages;

use App\Enums\PackageStatus;
use App\Models\IncomingPackage;
use Illuminate\Support\Facades\DB;

class ReceiveIncomingPackageAction
{
    /**
     * Приёмка входящей посылки на складе с указанием фактических параметров.
     */
    public function execute(
        IncomingPackage $package,
        float $weightKg,
        ?string $dimensions = null,
        ?string $internalNote = null,
        ?PackageStatus $status = null
    ): IncomingPackage {
        return DB::transaction(function () use ($package, $weightKg, $dimensions, $internalNote, $status) {
            $targetStatus = $status ?? (defined(PackageStatus::class . '::RECEIVED_IN_GREECE') ? PackageStatus::RECEIVED_IN_GREECE : PackageStatus::RECEIVED);

            $package->update([
                'status' => $targetStatus,
                'weight_kg' => $weightKg,
                'dimensions' => $dimensions ?? $package->dimensions,
                'internal_note' => $internalNote ?? $package->internal_note,
                'received_at' => now(),
            ]);

            return $package;
        });
    }
}