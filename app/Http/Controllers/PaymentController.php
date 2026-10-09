<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Загрузка квитанции/чека оплаты банковским переводом для заказа.
     */
    public function store(Request $request, Order $order): RedirectResponse
    {
        $user = Auth::user();
        $customer = $user->customer;

        if (!$customer || $order->customer_id !== $customer->id) {
            abort(403, 'У вас нет доступа к данному заказу.');
        }

        $request->validate([
            'receipt' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'], // до 10MB
        ]);

        $receiptPath = $request->file('receipt')->store('payments/receipts', 'private');

        // Получаем сумму из активного расчета (Quote) или ставим 0.00
        $amount = $order->activeQuote ? $order->activeQuote->total_amount : 0.00;

        Payment::create([
            'order_id' => $order->id,
            'customer_id' => $customer->id,
            'amount' => $amount,
            'currency' => 'EUR',
            'payment_method' => 'bank_transfer',
            'status' => 'pending',
            'receipt_file_path' => $receiptPath,
            'paid_at' => now(),
        ]);

        // Переводим статус заказа в состояние проверки оплаты (если такой статус предусмотрен)
        if (defined('\App\Enums\OrderStatus::PAYMENT_PENDING')) {
            $order->update(['status' => \App\Enums\OrderStatus::PAYMENT_PENDING]);
        }

        return redirect()->back()->with('success', 'Чек успешно загружен! Менеджер проверяет поступление средств.');
    }
}