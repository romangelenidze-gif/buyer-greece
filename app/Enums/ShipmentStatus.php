<?php

namespace App\Enums;

enum ShipmentStatus: string
{
    case PREPARING = 'preparing';
    case READY_FOR_CAMEX = 'ready_for_camex';
    case TRANSFERRED_TO_CAMEX = 'transferred_to_camex';
    case COMPLETED = 'completed';
    case PROBLEM = 'problem';

    public function getLabel(): string
    {
        return match ($this) {
            self::PREPARING => 'Формируется',
            self::READY_FOR_CAMEX => 'Готова для Camex',
            self::TRANSFERRED_TO_CAMEX => 'Передана в Camex',
            self::COMPLETED => 'Завершено',
            self::PROBLEM => 'Проблема',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PREPARING => 'gray',
            self::READY_FOR_CAMEX => 'warning',
            self::TRANSFERRED_TO_CAMEX => 'primary',
            self::COMPLETED => 'success',
            self::PROBLEM => 'danger',
        };
    }
}