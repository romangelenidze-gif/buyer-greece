<?php

namespace App\Observers;

use App\Models\Order;
use App\Notifications\OrderStatusChanged;

class OrderObserver
{
    public function updated(Order $order): void
    {
        if ($order->isDirty('status')) {
            $customer = $order->customer;

            if ($customer) {
                $customer->notify(new OrderStatusChanged($order));
            }
        }
    }
}