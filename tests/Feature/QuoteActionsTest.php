<?php

namespace Tests\Feature;

use App\Actions\Quotes\AcceptQuoteAction;
use App\Actions\Quotes\RejectQuoteAction;
use App\Enums\OrderStatus;
use App\Enums\QuoteStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuoteActionsTest extends TestCase
{
    use RefreshDatabase;

    private User $manager;
    private Order $order;
    private Quote $quote;

    protected function setUp(): void
    {
        parent::setUp();

        $customer = Customer::factory()->create();
        $this->manager = User::factory()->create(['role' => 'manager']);

        $this->order = Order::factory()->create([
            'customer_id' => $customer->id,
            'status' => OrderStatus::QUOTE_PREPARED,
        ]);

        $this->quote = Quote::factory()->create([
            'order_id' => $this->order->id,
            'created_by_user_id' => $this->manager->id,
            'status' => QuoteStatus::SENT,
            'product_total' => 100.00,
            'total' => 120.00,
        ]);

        $this->order->update(['active_quote_id' => $this->quote->id]);
    }

    public function test_customer_can_accept_quote_successfully(): void
    {
        $action = new AcceptQuoteAction();
        $action->execute($this->quote);

        $this->assertDatabaseHas('quotes', [
            'id' => $this->quote->id,
            'status' => QuoteStatus::ACCEPTED->value,
        ]);

        $this->assertNotNull($this->quote->fresh()->accepted_at);

        $this->assertDatabaseHas('orders', [
            'id' => $this->order->id,
            'status' => OrderStatus::AWAITING_PAYMENT->value,
        ]);
    }

    public function test_customer_can_reject_quote_successfully(): void
    {
        $action = new RejectQuoteAction();
        $action->execute($this->quote);

        $this->assertDatabaseHas('quotes', [
            'id' => $this->quote->id,
            'status' => QuoteStatus::REJECTED->value,
        ]);

        $this->assertNotNull($this->quote->fresh()->rejected_at);

        $this->assertDatabaseHas('orders', [
            'id' => $this->order->id,
            'status' => OrderStatus::CANCELLED->value,
        ]);
    }

    public function test_cannot_accept_already_accepted_quote(): void
    {
        $this->expectException(\DomainException::class);

        $this->quote->update(['status' => QuoteStatus::ACCEPTED]);

        $action = new AcceptQuoteAction();
        $action->execute($this->quote);
    }
}