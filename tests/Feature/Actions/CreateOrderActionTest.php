<?php

namespace Tests\Feature\Actions;

use App\Actions\Orders\CreateOrderAction;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateOrderActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_order_with_items_and_internal_note(): void
    {
        $customer = Customer::factory()->create();
        $type = OrderType::cases()[0];
        $itemsData = [
            [
                'product_url' => 'https://example.com/item1',
                'product_name' => 'Test Item 1',
                'quantity' => 2,
                'size' => 'M',
                'color' => 'Red',
            ],
            [
                'product_name' => 'Test Item 2',
                'quantity' => 1,
            ],
        ];

        $action = app(CreateOrderAction::class);
        $order = $action->execute($customer, $type, $itemsData, 'Internal note test');

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals($customer->id, $order->customer_id);
        $this->assertEquals($type, $order->type);
        $this->assertEquals(OrderStatus::UNDER_REVIEW, $order->status);
        $this->assertEquals('Internal note test', $order->internal_note);
        $this->assertCount(2, $order->items);
        $this->assertDatabaseHas('orders', ['id' => $order->id]);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_name' => 'Test Item 1',
            'quantity' => 2,
        ]);
    }
}