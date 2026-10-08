<?php

namespace Tests\Feature\Actions;

use App\Actions\Packages\AssignPackageToShipmentAction;
use App\Enums\PackageStatus;
use App\Models\IncomingPackage;
use App\Models\Shipment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignPackageToShipmentActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_assigns_package_to_shipment(): void
    {
        $package = IncomingPackage::factory()->create();
        $shipment = Shipment::factory()->create();

        $action = app(AssignPackageToShipmentAction::class);
        $result = $action->execute($package, $shipment);

        $this->assertEquals($shipment->id, $result->shipment_id);
        $this->assertEquals(PackageStatus::ASSIGNED_TO_SHIPMENT, $result->status);
        $this->assertDatabaseHas('incoming_packages', [
            'id' => $package->id,
            'shipment_id' => $shipment->id,
        ]);
    }
}