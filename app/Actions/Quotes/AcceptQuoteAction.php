<?php

namespace App\Actions\Quotes;

use App\Enums\OrderStatus;
use App\Enums\QuoteStatus;
use App\Models\Order;
use App\Models\Quote;
use Illuminate\Support\Facades\DB;

class AcceptQuoteAction
{
    public function execute(Quote $quote): void
    {
        DB::transaction(function () use ($quote) {
            /** @var Quote $lockedQuote */
            $lockedQuote = Quote::where('id', $quote->id)->lockForUpdate()->firstOrFail();

            if ($lockedQuote->status !== QuoteStatus::DRAFT && $lockedQuote->status !== QuoteStatus::SENT) {
                throw new \DomainException("Квоту в статусе {$lockedQuote->status->value} нельзя принять.");
            }

            $lockedQuote->update([
                'status' => QuoteStatus::ACCEPTED,
                'accepted_at' => now(),
            ]);

            /** @var Order $order */
            $order = Order::where('id', $lockedQuote->order_id)->lockForUpdate()->firstOrFail();
            $order->update([
                'status' => OrderStatus::AWAITING_PAYMENT,
            ]);
        });
    }
}