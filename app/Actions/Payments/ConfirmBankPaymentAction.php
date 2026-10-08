<?php

namespace App\Actions\Payments;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Notifications\PaymentConfirmedNotification;
use Illuminate\Support\Facades\DB;

class ConfirmBankPaymentAction
{
    public function execute(Payment $payment, int $confirmedByUserId): Payment
    {
        return DB::transaction(function () use ($payment, $confirmedByUserId) {
            // Блокируем запись платежа в базе данных (FOR UPDATE)
            $lockedPayment = Payment::where('id', $payment->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Если платеж уже подтвержден — возвращаем объект без повторной обработки и без дублирования писем
            $currentStatus = $lockedPayment->status->value ?? $lockedPayment->status;
            if ($currentStatus === PaymentStatus::CONFIRMED->value) {
                return $lockedPayment;
            }

            $lockedPayment->update([
                'status' => PaymentStatus::CONFIRMED,
                'confirmed_by_user_id' => $confirmedByUserId,
                'confirmed_at' => now(),
            ]);

            $order = Order::where('id', $lockedPayment->order_id)
                ->lockForUpdate()
                ->firstOrFail();

            $order->update([
                'status' => OrderStatus::PAID,
            ]);

            activity()
                ->performedOn($lockedPayment)
                ->causedBy($confirmedByUserId)
                ->withProperties([
                    'order_id' => $order->id,
                    'amount' => $lockedPayment->amount,
                    'currency' => $lockedPayment->currency,
                    'bank_reference' => $lockedPayment->bank_reference,
                ])
                ->log("Подтвержден платеж #{$lockedPayment->id} по заказу #{$order->public_order_number}");

            DB::afterCommit(function () use ($order, $lockedPayment) {
                if ($order->customer) {
                    $order->customer->notify(new PaymentConfirmedNotification($order, $lockedPayment));
                }
            });

            return $lockedPayment;
        });
    }
}