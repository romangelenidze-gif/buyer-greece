<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'public_order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'customer_id' => Customer::factory(),
            'type' => OrderType::BUY_FOR_ME->value,
            'status' => OrderStatus::NEW->value,
            'submitted_at' => now(),
        ];
    }
}