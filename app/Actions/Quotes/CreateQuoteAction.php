<?php

namespace App\Actions\Quotes;

use App\Enums\OrderStatus;
use App\Enums\QuoteStatus;
use App\Models\Order;
use App\Models\Quote;
use App\Notifications\QuotePreparedNotification;
use Illuminate\Support\Facades\DB;

class CreateQuoteAction
{
    public function execute(Order $order, array $quoteData, int $managerUserId): Quote
    {
        return DB::transaction(function () use ($order, $quoteData, $managerUserId) {
            if ($order->active_quote_id) {
                Quote::where('id', $order->active_quote_id)
                    ->update(['status' => QuoteStatus::SUPERSEDED->value]);
            }

            $quote = Quote::create([
                'order_id' => $order->id,
                'created_by_user_id' => $managerUserId,
                'status' => QuoteStatus::SENT->value,
                'product_total' => $quoteData['product_total'],
                'local_shipping' => $quoteData['local_shipping'] ?? 0.00,
                'buyer_fee' => $quoteData['buyer_fee'],
                'services_total' => $quoteData['services_total'] ?? 0.00,
                'other_costs' => $quoteData['other_costs'] ?? 0.00,
                'discount' => $quoteData['discount'] ?? 0.00,
                'total' => $quoteData['total'],
                'currency' => 'EUR',
                'valid_until' => now()->addHours(48),
                'notes' => $quoteData['notes'] ?? null,
            ]);

            $order->update([
                'active_quote_id' => $quote->id,
                'status' => OrderStatus::QUOTE_PREPARED->value,
            ]);

            DB::afterCommit(function () use ($order, $quote) {
                if ($order->customer) {
                    $order->customer->notify(new QuotePreparedNotification($order, $quote));
                }
            });

            return $quote;
        });
    }
}