<?php

namespace Tests\Feature\Actions;

use App\Actions\Packages\ReceivePackageAction;
use App\Enums\OrderStatus;
use App\Enums\PackageStatus;
use App\Models\Customer;
use App\Models\IncomingPackage;
use App\Models\Order;
use App\Notifications\PackageReceivedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ReceivePackageActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_receives_package_updates_order_and_notifies_customer(): void
    {
        Notification::fake();

        $customer = Customer::factory()->create();
        $order = Order::factory()->create(['customer_id' => $customer->id]);
        $package = IncomingPackage::factory()->create([
            'order_id' => $order->id,
            'customer_id' => $customer->id,
        ]);

        $action = app(ReceivePackageAction::class);
        $result = $action->execute($package, 3.4, 'photos/123.jpg');

        $this->assertEquals(PackageStatus::RECEIVED_IN_GREECE, $result->status);
        $this->assertEquals(3.4, $result->weight_kg);
        $this->assertEquals('photos/123.jpg', $result->photos_path);
        $this->assertNotNull($result->received_at);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatus::RECEIVED_IN_GREECE->value,
        ]);

        Notification::assertSentTo($customer, PackageReceivedNotification::class);
    }
}