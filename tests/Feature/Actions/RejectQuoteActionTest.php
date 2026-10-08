<?php

namespace Tests\Feature\Actions;

use App\Actions\Quotes\RejectQuoteAction;
use App\Enums\OrderStatus;
use App\Enums\QuoteStatus;
use App\Models\Order;
use App\Models\Quote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RejectQuoteActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_rejects_quote_and_cancels_order(): void
    {
        $order = Order::factory()->create();
        $quote = Quote::factory()->create([
            'order_id' => $order->id,
            'status' => QuoteStatus::SENT,
        ]);

        $action = app(RejectQuoteAction::class);
        $action->execute($quote);

        $this->assertEquals(QuoteStatus::REJECTED, $quote->fresh()->status);
        $this->assertNotNull($quote->fresh()->rejected_at);
        $this->assertEquals(OrderStatus::CANCELLED, $order->fresh()->status);
    }

    public function test_throws_exception_when_rejecting_invalid_status(): void
    {
        $this->expectException(\DomainException::class);

        $quote = Quote::factory()->create([
            'status' => QuoteStatus::ACCEPTED,
        ]);

        $action = app(RejectQuoteAction::class);
        $action->execute($quote);
    }
}