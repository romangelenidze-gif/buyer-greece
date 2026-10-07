<?php

namespace App\Enums;

enum PackageSourceType: string
{
    case BUYER_ORDER = 'buyer_order';
    case SELF_PURCHASE = 'self_purchase';
    case OTHER = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::BUYER_ORDER => 'Выкуп байером',
            self::SELF_PURCHASE => 'Самостоятельный выкуп',
            self::OTHER => 'Прочее',
        };
    }
}