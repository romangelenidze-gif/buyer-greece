<?php

namespace Tests\Feature;

use App\Actions\Packages\ReceiveIncomingPackageAction;
use App\Enums\PackageStatus;
use App\Models\Customer;
use App\Models\IncomingPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReceiveIncomingPackageTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_receive_incoming_package_and_update_weight_and_status(): void
    {
        $customer = Customer::factory()->create();

        $package = IncomingPackage::factory()->create([
            'customer_id' => $customer->id,
            'status' => PackageStatus::EXPECTED,
            'weight_kg' => 0,
            'received_at' => null,
        ]);

        $action = new ReceiveIncomingPackageAction();
        $updatedPackage = $action->execute(
            package: $package,
            weightKg: 2.45,
            dimensions: '30x20x15',
            internalNote: 'Упаковка целая'
        );

        $this->assertEquals(2.45, $updatedPackage->weight_kg);
        $this->assertEquals('30x20x15', $updatedPackage->dimensions);
        $this->assertNotNull($updatedPackage->received_at);

        $this->assertDatabaseHas('incoming_packages', [
            'id' => $package->id,
            'weight_kg' => 2.45,
            'dimensions' => '30x20x15',
            'internal_note' => 'Упаковка целая',
        ]);
    }
}