<?php

namespace Tests\Feature\Actions;

use App\Actions\Quotes\ExpireQuoteAction;
use App\Enums\OrderStatus;
use App\Enums\QuoteStatus;
use App\Models\Order;
use App\Models\Quote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpireQuoteActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_expires_sent_quote_and_updates_order_status(): void
    {
        $order = Order::factory()->create();
        $quote = Quote::factory()->create([
            'order_id' => $order->id,
            'status' => QuoteStatus::SENT,
        ]);
        $order->update(['active_quote_id' => $quote->id]);

        $action = app(ExpireQuoteAction::class);
        $action->execute($quote);

        $this->assertEquals(QuoteStatus::EXPIRED, $quote->fresh()->status);
        $this->assertEquals(OrderStatus::QUOTE_EXPIRED, $order->fresh()->status);
    }

    public function test_throws_exception_when_expiring_invalid_quote_status(): void
    {
        $this->expectException(\DomainException::class);

        $quote = Quote::factory()->create([
            'status' => QuoteStatus::ACCEPTED,
        ]);

        $action = app(ExpireQuoteAction::class);
        $action->execute($quote);
    }
}