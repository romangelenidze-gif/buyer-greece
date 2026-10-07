<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Shipment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Shipment>
 */
class ShipmentFactory extends Factory
{
    protected $model = Shipment::class;

    public function definition(): array
    {
        return [
            'public_shipment_number' => 'SHP-' . strtoupper(fake()->unique()->bothify('??###?##')),
            'customer_id' => Customer::factory(),
            'status' => 'preparing',
            'carrier' => 'CAMEX',
            'destination_country' => fake()->country(),
            'weight_kg' => 0,
            'camex_tracking_number' => null,
            'camex_status' => null,
            'transferred_to_camex_at' => null,
            'dispatched_at' => null,
            'delivered_at' => null,
            'internal_note' => null,
        ];
    }
}