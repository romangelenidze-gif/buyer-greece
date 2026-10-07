<?php

namespace App\Notifications;

use App\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShipmentTransferredToCamexNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Shipment $shipment
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Отправление #{$this->shipment->public_shipment_number} передано в Camex")
            ->greeting("Здравствуйте, {$notifiable->first_name}!")
            ->line("Ваше отправление передано логистической компании Camex для транспортировки в Грузию.")
            ->line("Трек-номер Camex: {$this->shipment->camex_tracking_number}")
            ->line("Наш этап обслуживания завершен. Дальнейшая перевозка и отслеживание осуществляются сервисом Camex.");
    }
}