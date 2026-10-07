<?php

namespace App\Actions\Quotes;

use App\Enums\OrderStatus;
use App\Enums\QuoteStatus;
use App\Models\Order;
use App\Models\Quote;
use Illuminate\Support\Facades\DB;

class RejectQuoteAction
{
    public function execute(Quote $quote): void
    {
        if ($quote->status !== QuoteStatus::DRAFT && $quote->status !== QuoteStatus::SENT) {
            throw new \DomainException("Квоту в статусе {$quote->status->value} нельзя отклонить.");
        }

        DB::transaction(function () use ($quote) {
            $quote->update([
                'status' => QuoteStatus::REJECTED,
                'rejected_at' => now(),
            ]);

            /** @var Order $order */
            $order = $quote->order;
            $order->update([
                'status' => OrderStatus::CANCELLED,
            ]);
        });
    }
}