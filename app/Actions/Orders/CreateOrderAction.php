<?php

namespace App\Actions\Orders;

use App\Models\Order;
use App\Models\Customer;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateOrderAction
{
    public function execute(Customer $customer, OrderType $type, array $itemsData, ?string $internalNote = null): Order
    {
        return DB::transaction(function () use ($customer, $type, $itemsData, $internalNote) {
            $order = Order::create([
                'public_order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'customer_id' => $customer->id,
                'type' => $type->value,
                'status' => OrderStatus::UNDER_REVIEW->value,
                'internal_note' => $internalNote,
                'submitted_at' => now(),
            ]);

            foreach ($itemsData as $item) {
                $order->items()->create([
                    'product_url' => $item['product_url'] ?? null,
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'] ?? 1,
                    'size' => $item['size'] ?? null,
                    'color' => $item['color'] ?? null,
                    'model' => $item['model'] ?? null,
                    'options' => $item['options'] ?? null,
                    'requested_comment' => $item['requested_comment'] ?? null,
                ]);
            }

            return $order;
        });
    }
}