<?php

namespace App\Enums;

enum PackageStatus: string
{
    case EXPECTED = 'expected';
    case RECEIVED_IN_GREECE = 'received_in_greece';
    case READY_FOR_SHIPMENT = 'ready_for_shipment';
    case ASSIGNED_TO_SHIPMENT = 'assigned_to_shipment';
    case UNIDENTIFIED = 'unidentified';
    case PROBLEM = 'problem';

    public function getLabel(): string
    {
        return match ($this) {
            self::EXPECTED => 'Ожидается',
            self::RECEIVED_IN_GREECE => 'Получена в Греции',
            self::READY_FOR_SHIPMENT => 'Готова к отправке',
            self::ASSIGNED_TO_SHIPMENT => 'Включена в партию',
            self::UNIDENTIFIED => 'Неопознанная',
            self::PROBLEM => 'Проблема / Задержана',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::EXPECTED => 'gray',
            self::RECEIVED_IN_GREECE => 'info',
            self::READY_FOR_SHIPMENT => 'primary',
            self::ASSIGNED_TO_SHIPMENT => 'success',
            self::UNIDENTIFIED => 'warning',
            self::PROBLEM => 'danger',
        };
    }
}