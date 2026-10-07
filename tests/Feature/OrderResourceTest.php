<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\OrderResource\Pages\CreateOrder;
use App\Filament\Resources\OrderResource\Pages\EditOrder;
use App\Filament\Resources\OrderResource\Pages\ListOrders;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrderResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'role' => 'super_admin',
        ]);
        $this->customer = Customer::factory()->create();
        $this->actingAs($this->admin);
    }

    public function test_can_render_orders_list_page(): void
    {
        $orders = Order::factory()->count(3)->create([
            'customer_id' => $this->customer->id,
        ]);

        Livewire::test(ListOrders::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords($orders);
    }

    public function test_can_create_order_via_filament_form(): void
    {
        Livewire::test(CreateOrder::class)
            ->fillForm([
                'customer_id' => $this->customer->id,
                'type' => 'buy_for_me',
                'status' => 'pending',
                'total_amount' => 150.00,
                'items' => [
                    [
                        'product_name' => 'Тестовый товар',
                        'quantity' => 1,
                        'price' => 150.00,
                    ],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('orders', [
            'customer_id' => $this->customer->id,
            'type' => 'buy_for_me',
            'status' => 'pending',
        ]);
    }

    public function test_can_update_order_status_via_filament(): void
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'status' => 'pending',
        ]);

        Livewire::test(EditOrder::class, ['record' => $order->getRouteKey()])
            ->fillForm([
                'status' => 'processing',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'processing',
        ]);
    }
}