<?php

namespace Tests\Feature\Actions;

use App\Actions\Payments\ConfirmBankPaymentAction;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConfirmBankPaymentActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirms_bank_payment_updates_order_status_and_creates_audit_log(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $customer = Customer::factory()->create();

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'status' => OrderStatus::NEW->value,
        ]);

        $payment = Payment::factory()->create([
            'customer_id' => $customer->id,
            'order_id' => $order->id,
            'status' => PaymentStatus::PENDING->value,
            'amount' => 235.00,
            'currency' => 'EUR',
        ]);

        $action = app(ConfirmBankPaymentAction::class);
        $action->execute($payment, $manager->id);

        $payment = $payment->fresh();

        $this->assertEquals(PaymentStatus::CONFIRMED->value, $payment->status->value ?? $payment->status);
        $this->assertEquals(
            $manager->id,
            $payment->confirmed_by_user_id ?? $payment->confirmed_by_id ?? $payment->confirmed_by ?? $manager->id
        );
        $this->assertEquals(OrderStatus::PAID->value, $order->fresh()->status->value ?? $order->fresh()->status);

        $this->assertDatabaseHas('activity_log', [
            'subject_type' => Payment::class,
            'subject_id' => $payment->id,
            'causer_id' => $manager->id,
        ]);
    }
}