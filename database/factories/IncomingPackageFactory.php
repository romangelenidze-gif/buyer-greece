<?php

namespace Database\Factories;

use App\Enums\PackageSourceType;
use App\Enums\PackageStatus;
use App\Models\Customer;
use App\Models\IncomingPackage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IncomingPackage>
 */
class IncomingPackageFactory extends Factory
{
    protected $model = IncomingPackage::class;

    public function definition(): array
    {
        return [
            'public_package_number' => 'PKG-' . strtoupper(fake()->unique()->bothify('??###?##')),
            'customer_id' => Customer::factory(),
            'order_id' => null,
            'shipment_id' => null,
            'source_type' => PackageSourceType::SELF_PURCHASE,
            'store_name' => fake()->company(),
            'store_tracking_number' => 'TRK' . strtoupper(fake()->bothify('??###?##')),
            'description' => fake()->sentence(),
            'expected_date' => now()->addDays(3),
            'received_at' => null,
            'weight_kg' => 0,
            'dimensions' => null,
            'status' => PackageStatus::EXPECTED,
            'photos_path' => null,
            'internal_note' => null,
        ];
    }
}