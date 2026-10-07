<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabel = $this->order->status->getLabel();

        return (new MailMessage)
            ->subject("Изменение статуса заказа № {$this->order->public_order_number}")
            ->greeting("Здравствуйте, {$notifiable->first_name}!")
            ->line("Статус вашего заказа № **{$this->order->public_order_number}** был изменен.")
            ->line("Новый статус: **{$statusLabel}**")
            ->action('Просмотреть заказ', url('/dashboard/orders/' . $this->order->id))
            ->line('Благодарим за использование нашего сервиса!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->public_order_number,
            'status' => $this->order->status->value,
            'title' => "Обновление заказа № {$this->order->public_order_number}",
            'message' => "Статус вашего заказа изменен на: \"{$this->order->status->getLabel()}\"",
        ];
    }
}