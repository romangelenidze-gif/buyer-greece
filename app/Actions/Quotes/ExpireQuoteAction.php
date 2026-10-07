<?php

namespace App\Actions\Quotes;

use App\Enums\OrderStatus;
use App\Enums\QuoteStatus;
use App\Models\Order;
use App\Models\Quote;
use Illuminate\Support\Facades\DB;

class ExpireQuoteAction
{
    public function execute(Quote $quote): void
    {
        if ($quote->status === QuoteStatus::EXPIRED) {
            return;
        }

        if ($quote->status !== QuoteStatus::SENT && $quote->status !== QuoteStatus::DRAFT) {
            throw new \DomainException("Квоту со статусом {$quote->status->value} нельзя перевести в истёкшую.");
        }

        DB::transaction(function () use ($quote) {
            $quote->update([
                'status' => QuoteStatus::EXPIRED,
            ]);

            /** @var Order $order */
            $order = $quote->order;
            if ($order && $order->active_quote_id === $quote->id) {
                $order->update([
                    'status' => OrderStatus::QUOTE_EXPIRED,
                ]);
            }
        });
    }
}