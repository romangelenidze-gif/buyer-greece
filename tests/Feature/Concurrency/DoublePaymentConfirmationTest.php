<?php

namespace Tests\Feature\Concurrency;

use App\Actions\Payments\ConfirmBankPaymentAction;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoublePaymentConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_prevents_double_payment_confirmation(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $order = Order::factory()->create(['status' => OrderStatus::AWAITING_PAYMENT]);
        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'status' => PaymentStatus::PENDING,
            'amount' => 100.00,
        ]);

        $action = app(ConfirmBankPaymentAction::class);

        // Первый вызов — успешное подтверждение
        $confirmedPayment = $action->execute($payment, $admin->id);
        $this->assertEquals(PaymentStatus::CONFIRMED, $confirmedPayment->status);
        $this->assertEquals(OrderStatus::PAID, $order->fresh()->status);

        // Второй вызов — попытка повторного подтверждения не меняет статус и не вызывает ошибок
        $secondAttempt = $action->execute($payment->fresh(), $admin->id);
        $this->assertEquals(PaymentStatus::CONFIRMED, $secondAttempt->status);
    }
}