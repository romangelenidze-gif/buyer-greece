<?php

namespace Tests\Feature;

use App\Actions\Packages\AssignPackageToShipmentAction;
use App\Enums\PackageStatus;
use App\Models\Customer;
use App\Models\IncomingPackage;
use App\Models\Shipment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignPackageToShipmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_assign_package_to_shipment(): void
    {
        $customer = Customer::factory()->create();

        $shipment = Shipment::factory()->create([
            'customer_id' => $customer->id,
        ]);

        $package = IncomingPackage::factory()->create([
            'customer_id' => $customer->id,
            'status' => PackageStatus::EXPECTED,
        ]);

        $action = new AssignPackageToShipmentAction();
        $updatedPackage = $action->execute($package, $shipment);

        $this->assertEquals($shipment->id, $updatedPackage->shipment_id);
        $this->assertEquals(PackageStatus::ASSIGNED_TO_SHIPMENT, $updatedPackage->status);

        $this->assertDatabaseHas('incoming_packages', [
            'id' => $package->id,
            'shipment_id' => $shipment->id,
            'status' => PackageStatus::ASSIGNED_TO_SHIPMENT->value,
        ]);
    }
}