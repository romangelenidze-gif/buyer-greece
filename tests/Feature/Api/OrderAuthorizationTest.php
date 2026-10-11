<?php

namespace Tests\Feature\Api;

use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_allows_customer_to_view_their_own_order(): void
    {
        $customer = Customer::factory()->create();
        $user = User::factory()->create([
            'role' => 'customer',
            'customer_id' => $customer->id,
        ]);

        $order = Order::factory()->create(['customer_id' => $customer->id]);

        $this->actingAs($user)
            ->get(route('orders.show', $order))
            ->assertStatus(200);
    }

    public function test_prevents_customer_from_viewing_another_customers_order(): void
    {
        $customerA = Customer::factory()->create();
        $customerB = Customer::factory()->create();

        $userA = User::factory()->create([
            'role' => 'customer',
            'customer_id' => $customerA->id,
        ]);

        $orderB = Order::factory()->create(['customer_id' => $customerB->id]);

        $this->actingAs($userA)
            ->get(route('orders.show', $orderB))
            ->assertStatus(403);
    }
}