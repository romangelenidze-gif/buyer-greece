<?php

namespace Tests\Feature\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrderMediaControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_download_their_own_receipt(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put('receipts/test.pdf', 'fake content');

        $customer = Customer::factory()->create();
        $user = User::factory()->create([
            'role' => 'customer',
            'customer_id' => $customer->id,
        ]);

        $order = Order::factory()->create(['customer_id' => $customer->id]);
        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'customer_id' => $customer->id,
            'receipt_file_path' => 'receipts/test.pdf',
        ]);

        $response = $this->actingAs($user)->get(route('orders.payments.receipt', [
            'order' => $order->id,
            'payment' => $payment->id,
        ]));

        $response->assertOk();
    }

    public function test_customer_cannot_download_another_customers_receipt(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put('receipts/test.pdf', 'fake content');

        $customer1 = Customer::factory()->create();
        $customer2 = Customer::factory()->create();

        $user1 = User::factory()->create([
            'role' => 'customer',
            'customer_id' => $customer1->id,
        ]);

        $order2 = Order::factory()->create(['customer_id' => $customer2->id]);
        $payment2 = Payment::factory()->create([
            'order_id' => $order2->id,
            'customer_id' => $customer2->id,
            'receipt_file_path' => 'receipts/test.pdf',
        ]);

        $response = $this->actingAs($user1)->get(route('orders.payments.receipt', [
            'order' => $order2->id,
            'payment' => $payment2->id,
        ]));

        $response->assertStatus(403);
    }
}