<?php

namespace App\Actions\Quotes;

use App\Models\Order;
use App\Models\Quote;

class CalculateBuyForMeQuoteAction
{
    public function execute(Order $order, float $commissionPercent, float $shippingCostEur): Quote
    {
        $order->load('items');

        $itemsTotal = (float) $order->items->sum(function ($item) {
            return (float) $item->unit_price * (int) $item->quantity;
        });

        $commission = $itemsTotal * ($commissionPercent / 100);
        $total = $itemsTotal + $commission + $shippingCostEur;

        $quote = Quote::create([
            'order_id'       => $order->id,
            'product_total'  => $itemsTotal,
            'buyer_fee'      => $commission,
            'local_shipping' => $shippingCostEur,
            'total'          => $total,
        ]);

        $order->update(['active_quote_id' => $quote->id]);

        return $quote;
    }
}