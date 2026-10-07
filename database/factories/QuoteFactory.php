<?php

namespace Database\Factories;

use App\Enums\QuoteStatus;
use App\Models\Order;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quote>
 */
class QuoteFactory extends Factory
{
    protected $model = Quote::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'created_by_user_id' => User::factory(),
            'status' => QuoteStatus::SENT,
            'product_total' => 100.00,
            'local_shipping' => 10.00,
            'buyer_fee' => 10.00,
            'services_total' => 0.00,
            'other_costs' => 0.00,
            'discount' => 0.00,
            'total' => 120.00,
            'currency' => 'EUR',
            'valid_until' => now()->addHours(48),
        ];
    }
}