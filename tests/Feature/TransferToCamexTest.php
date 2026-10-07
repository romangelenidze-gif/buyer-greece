<?php

namespace Tests\Feature;

use App\Actions\Shipments\TransferToCamexAction;
use App\Enums\OrderStatus;
use App\Enums\PackageStatus;
use App\Enums\ShipmentStatus;
use App\Models\Customer;
use App\Models\IncomingPackage;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\User;
use App\Notifications\ShipmentTransferredToCamexNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TransferToCamexTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_transfer_shipment_to_camex_and_update_all_related_entities(): void
    {
        Notification::fake();

        $customer = Customer::factory()->create();
        $manager = User::factory()->create();

        $shipment = Shipment::factory()->create([
            'customer_id' => $customer->id,
            'status' => ShipmentStatus::PREPARING ?? 'preparing',
            'camex_tracking_number' => null,
            'transferred_to_camex_at' => null,
        ]);

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'completed_at' => null,
        ]);

        $package = IncomingPackage::factory()->create([
            'customer_id' => $customer->id,
            'shipment_id' => $shipment->id,
            'order_id' => $order->id,
            'status' => PackageStatus::EXPECTED,
        ]);

        $action = new TransferToCamexAction();
        $updatedShipment = $action->execute(
            shipment: $shipment,
            camexTrackingNumber: 'CAMEX-12345678',
            camexStatus: 'registered',
            managerUserId: $manager->id
        );

        // 1. Проверяем обновление отправления
        $this->assertEquals('CAMEX-12345678', $updatedShipment->camex_tracking_number);
        $this->assertEquals('registered', $updatedShipment->camex_status);
        $this->assertNotNull($updatedShipment->transferred_to_camex_at);
        $this->assertEquals(ShipmentStatus::TRANSFERRED_TO_CAMEX, $updatedShipment->status);

        // 2. Проверяем обновление статуса посылки
        $package->refresh();
        $this->assertEquals(PackageStatus::ASSIGNED_TO_SHIPMENT, $package->status);

        // 3. Проверяем завершение связанного заказа
        $order->refresh();
        $this->assertEquals(OrderStatus::COMPLETED, $order->status);
        $this->assertNotNull($order->completed_at);

        // 4. Проверяем отправку уведомления клиенту
        Notification::assertSentTo(
            $customer,
            ShipmentTransferredToCamexNotification::class
        );
    }
}