<?php

namespace Tests\Feature\Actions;

use App\Actions\Quotes\CreateQuoteAction;
use App\Enums\OrderStatus;
use App\Enums\QuoteStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Quote;
use App\Models\User;
use App\Notifications\QuotePreparedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CreateQuoteActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_quote_and_supersedes_existing_active_quote(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $customer = Customer::factory()->create();
        $order = Order::factory()->create(['customer_id' => $customer->id]);

        $oldQuote = Quote::factory()->create([
            'order_id' => $order->id,
            'status' => QuoteStatus::SENT->value,
        ]);
        $order->update(['active_quote_id' => $oldQuote->id]);

        $quoteData = [
            'product_total' => 100.00,
            'local_shipping' => 10.00,
            'buyer_fee' => 15.00,
            'services_total' => 5.00,
            'other_costs' => 0.00,
            'discount' => 0.00,
            'total' => 130.00,
            'notes' => 'Test notes',
        ];

        $action = app(CreateQuoteAction::class);
        $newQuote = $action->execute($order, $quoteData, $user->id);

        $this->assertEquals(QuoteStatus::SUPERSEDED, $oldQuote->fresh()->status);
        $this->assertEquals($newQuote->id, $order->fresh()->active_quote_id);
        $this->assertEquals(OrderStatus::QUOTE_PREPARED, $order->fresh()->status);

        Notification::assertSentTo($customer, QuotePreparedNotification::class);
    }
}