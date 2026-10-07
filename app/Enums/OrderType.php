<?php

namespace App\Enums;

enum OrderType: string
{
    case BUY_FOR_ME = 'buy_for_me';
    case MANUAL_ORDER = 'manual_order';

    public function getLabel(): string
    {
        return match ($this) {
            self::BUY_FOR_ME => 'Выкупить для меня',
            self::MANUAL_ORDER => 'Ручной заказ',
        };
    }
}