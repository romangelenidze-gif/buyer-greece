<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'order_id' => Order::factory(),
            'amount' => 235.00,
            'currency' => 'EUR',
            'status' => PaymentStatus::PENDING->value,
            'payment_method' => 'bank_transfer',
        ];
    }
}