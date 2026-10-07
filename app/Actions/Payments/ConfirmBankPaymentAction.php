<?php

namespace App\Actions\Payments;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Notifications\PaymentConfirmedNotification;
use Illuminate\Support\Facades\DB;

class ConfirmBankPaymentAction
{
    public function execute(Payment $payment, int $confirmedByUserId): Payment
    {
        return DB::transaction(function () use ($payment, $confirmedByUserId) {
            $payment->update([
                'status' => PaymentStatus::CONFIRMED->value,
                'confirmed_by_user_id' => $confirmedByUserId,
                'confirmed_at' => now(),
            ]);

            $order = $payment->order;
            $order->update([
                'status' => OrderStatus::PAID->value,
            ]);

            activity()
                ->performedOn($payment)
                ->causedBy($confirmedByUserId)
                ->withProperties([
                    'order_id' => $order->id,
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'bank_reference' => $payment->bank_reference,
                ])
                ->log("Подтвержден платеж #{$payment->id} по заказу #{$order->public_order_number}");

            DB::afterCommit(function () use ($order, $payment) {
                if ($order->customer) {
                    $order->customer->notify(new PaymentConfirmedNotification($order, $payment));
                }
            });

            return $payment;
        });
    }
}