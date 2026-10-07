<?php

namespace App\Notifications;

use App\Models\IncomingPackage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PackageReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public IncomingPackage $package
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Посылка #{$this->package->public_package_number} получена на складе в Греции")
            ->greeting("Здравствуйте, {$notifiable->first_name}!")
            ->line("Ваша посылка поступила на наш склад в Греции.")
            ->line("Магазин / Трек: " . ($this->package->store_tracking_number ?? 'Не указан'))
            ->line("Вес: " . ($this->package->weight_kg ? "{$this->package->weight_kg} кг" : 'Уточняется'))
            ->action('Посмотреть посылку', url('/cabinet/packages/' . $this->package->id));
    }
}