<?php

namespace App\Actions\Packages;

use App\Models\IncomingPackage;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class AttachPackageToOrderAction
{
    /**
     * Привязка посылки к заказу и соответствующему клиенту.
     */
    public function execute(IncomingPackage $package, Order $order): IncomingPackage
    {
        return DB::transaction(function () use ($package, $order) {
            $package->update([
                'order_id' => $order->id,
                'customer_id' => $order->customer_id,
            ]);

            return $package;
        });
    }
}