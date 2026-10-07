<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case BANK_TRANSFER = 'bank_transfer';
    case CARD = 'card';
    case STRIPE = 'stripe';
    case CASH = 'cash';

    public function getLabel(): string
    {
        return match ($this) {
            self::BANK_TRANSFER => 'Банковский перевод',
            self::CARD => 'Банковская карта',
            self::STRIPE => 'Stripe / Онлайн',
            self::CASH => 'Наличные',
        };
    }
}