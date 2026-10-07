<?php

namespace Tests\Feature;

use App\Actions\Quotes\ExpireQuoteAction;
use App\Enums\OrderStatus;
use App\Enums\QuoteStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpireQuoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_expire_quote_and_update_order_status(): void
    {
        $customer = Customer::factory()->create();
        $manager = User::factory()->create(['role' => 'manager']);

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'status' => OrderStatus::QUOTE_PREPARED,
        ]);

        $quote = Quote::factory()->create([
            'order_id' => $order->id,
            'created_by_user_id' => $manager->id,
            'status' => QuoteStatus::SENT,
            'valid_until' => now()->subHour(),
        ]);

        $order->update(['active_quote_id' => $quote->id]);

        $action = new ExpireQuoteAction();
        $action->execute($quote);

        $this->assertDatabaseHas('quotes', [
            'id' => $quote->id,
            'status' => QuoteStatus::EXPIRED->value,
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatus::QUOTE_EXPIRED->value,
        ]);
    }

    public function test_artisan_command_expires_past_due_quotes(): void
    {
        $customer = Customer::factory()->create();
        $manager = User::factory()->create(['role' => 'manager']);

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'status' => OrderStatus::QUOTE_PREPARED,
        ]);

        $quote = Quote::factory()->create([
            'order_id' => $order->id,
            'created_by_user_id' => $manager->id,
            'status' => QuoteStatus::SENT,
            'valid_until' => now()->subHour(),
        ]);

        $order->update(['active_quote_id' => $quote->id]);

        $this->artisan('quotes:expire')
            ->assertExitCode(0);

        $this->assertDatabaseHas('quotes', [
            'id' => $quote->id,
            'status' => QuoteStatus::EXPIRED->value,
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatus::QUOTE_EXPIRED->value,
        ]);
    }
}