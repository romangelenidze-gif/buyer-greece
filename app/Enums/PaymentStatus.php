<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case REJECTED = 'rejected';
    case MISMATCH = 'mismatch';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'Ожидает проверки',
            self::CONFIRMED => 'Подтверждён',
            self::REJECTED => 'Отклонён',
            self::MISMATCH => 'Расхождение суммы',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::CONFIRMED => 'success',
            self::REJECTED => 'danger',
            self::MISMATCH => 'warning',
        };
    }
}