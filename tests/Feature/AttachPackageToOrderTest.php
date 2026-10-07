<?php

namespace Tests\Feature;

use App\Actions\Packages\AttachPackageToOrderAction;
use App\Enums\OrderStatus;
use App\Enums\PackageStatus;
use App\Models\Customer;
use App\Models\IncomingPackage;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttachPackageToOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_attach_unassigned_package_to_order_and_customer(): void
    {
        $customer = Customer::factory()->create();

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'status' => OrderStatus::QUOTE_PREPARED,
        ]);

        $package = IncomingPackage::factory()->create([
            'customer_id' => null,
            'order_id' => null,
            'status' => PackageStatus::EXPECTED,
        ]);

        $action = new AttachPackageToOrderAction();
        $updatedPackage = $action->execute($package, $order);

        $this->assertEquals($order->id, $updatedPackage->order_id);
        $this->assertEquals($customer->id, $updatedPackage->customer_id);

        $this->assertDatabaseHas('incoming_packages', [
            'id' => $package->id,
            'order_id' => $order->id,
            'customer_id' => $customer->id,
        ]);
    }
}