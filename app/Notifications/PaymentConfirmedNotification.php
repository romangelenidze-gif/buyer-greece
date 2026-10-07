<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order,
        public Payment $payment
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Оплата по заказу #{$this->order->public_order_number} подтверждена")
            ->greeting("Здравствуйте, {$notifiable->first_name}!")
            ->line("Мы успешно подтвердили ваш платеж на сумму {$this->payment->amount} {$this->payment->currency}.")
            ->line("Заказ передан в отдел закупки. Мы выкупим ваши товары в ближайшее время.");
    }
}