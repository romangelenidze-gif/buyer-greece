<?php

namespace Tests\Feature\Actions;

use App\Actions\Quotes\AcceptQuoteAction;
use App\Enums\OrderStatus;
use App\Enums\QuoteStatus;
use App\Models\Order;
use App\Models\Quote;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcceptQuoteActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_accepts_quote_and_updates_order_status(): void
    {
        $order = Order::factory()->create();
        $quote = Quote::factory()->create([
            'order_id' => $order->id,
            'status' => QuoteStatus::SENT,
        ]);

        $action = app(AcceptQuoteAction::class);
        $action->execute($quote);

        $this->assertEquals(QuoteStatus::ACCEPTED, $quote->fresh()->status);
        $this->assertNotNull($quote->fresh()->accepted_at);
        $this->assertEquals(OrderStatus::AWAITING_PAYMENT, $order->fresh()->status);
    }

    public function test_throws_exception_when_accepting_invalid_status(): void
    {
        $this->expectException(DomainException::class);

        $quote = Quote::factory()->create([
            'status' => QuoteStatus::ACCEPTED,
        ]);

        $action = app(AcceptQuoteAction::class);
        $action->execute($quote);
    }

    public function test_prevents_double_quote_acceptance(): void
    {
        $order = Order::factory()->create();
        $quote = Quote::factory()->create([
            'order_id' => $order->id,
            'status' => QuoteStatus::SENT,
        ]);

        $action = app(AcceptQuoteAction::class);
        $action->execute($quote);

        $this->expectException(DomainException::class);
        $action->execute($quote->fresh());
    }
}