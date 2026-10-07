<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuotePreparedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order,
        public Quote $quote
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url('/cabinet/orders/' . $this->order->id);

        return (new MailMessage)
            ->subject("Готов расчёт по заказу #{$this->order->public_order_number}")
            ->greeting("Здравствуйте, {$notifiable->first_name}!")
            ->line("Менеджер подготовил коммерческий расчёт для вашего заказа #{$this->order->public_order_number}.")
            ->line("Итоговая сумма к оплате: {$this->quote->total} {$this->quote->currency}")
            ->action('Просмотреть и принять расчёт', $url)
            ->line('Обратите внимание: расчёт действителен в течение 48 часов.');
    }
}