<?php

namespace Tests\Feature\Actions;

use App\Actions\Quotes\CalculateBuyForMeQuoteAction;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalculateBuyForMeQuoteActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculates_buy_for_me_quote_correctly_and_creates_active_quote(): void
    {
        $order = Order::factory()->create([
            'status' => OrderStatus::NEW->value,
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'unit_price' => 100.00,
            'quantity' => 2,
        ]);

        $action = app(CalculateBuyForMeQuoteAction::class);
        $quote = $action->execute($order, commissionPercent: 10, shippingCostEur: 15.00);

        $this->assertEquals(200.00, (float) $quote->items_total_eur);
        $this->assertEquals(20.00, (float) $quote->commission_eur);
        $this->assertEquals(15.00, (float) $quote->shipping_eur);
        $this->assertEquals(235.00, (float) $quote->total_eur);
        $this->assertEquals($quote->id, $order->fresh()->active_quote_id);
    }
}