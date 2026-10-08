<?php

namespace Tests\Feature\Actions;

use App\Actions\Packages\AttachPackageToOrderAction;
use App\Models\IncomingPackage;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttachPackageToOrderActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_attaches_package_to_order_and_customer(): void
    {
        $package = IncomingPackage::factory()->create();
        $order = Order::factory()->create();

        $action = app(AttachPackageToOrderAction::class);
        $result = $action->execute($package, $order);

        $this->assertEquals($order->id, $result->order_id);
        $this->assertEquals($order->customer_id, $result->customer_id);
        $this->assertDatabaseHas('incoming_packages', [
            'id' => $package->id,
            'order_id' => $order->id,
            'customer_id' => $order->customer_id,
        ]);
    }
}