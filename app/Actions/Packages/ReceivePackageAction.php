<?php

namespace App\Actions\Packages;

use App\Enums\OrderStatus;
use App\Enums\PackageStatus;
use App\Models\IncomingPackage;
use App\Notifications\PackageReceivedNotification;
use Illuminate\Support\Facades\DB;

class ReceivePackageAction
{
    public function execute(IncomingPackage $package, ?float $weightKg = null, ?string $photosPath = null): IncomingPackage
    {
        return DB::transaction(function () use ($package, $weightKg, $photosPath) {
            $package->update([
                'status' => PackageStatus::RECEIVED_IN_GREECE->value,
                'received_at' => now(),
                'weight_kg' => $weightKg ?? $package->weight_kg,
                'photos_path' => $photosPath ?? $package->photos_path,
            ]);

            if ($package->order) {
                $package->order->update([
                    'status' => OrderStatus::RECEIVED_IN_GREECE->value,
                ]);
            }

            DB::afterCommit(function () use ($package) {
                if ($package->customer) {
                    $package->customer->notify(new PackageReceivedNotification($package));
                }
            });

            return $package;
        });
    }
}