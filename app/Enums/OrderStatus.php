<?php

namespace App\Enums;

enum OrderStatus: string
{
    case NEW = 'new';
    case PENDING = 'pending';
    case UNDER_REVIEW = 'under_review';
    case PROCESSING = 'processing';
    case APPROVED = 'approved';
    case PAID = 'paid';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case REJECTED = 'rejected';

    public function getLabel(): string
    {
        return match ($this) {
            self::NEW => 'Новый',
            self::PENDING => 'Ожидает',
            self::UNDER_REVIEW => 'На проверке',
            self::PROCESSING => 'В обработке',
            self::APPROVED => 'Одобрен',
            self::PAID => 'Оплачен',
            self::COMPLETED => 'Завершён',
            self::CANCELLED => 'Отменён',
            self::REJECTED => 'Отклонён',
        };
    }
}