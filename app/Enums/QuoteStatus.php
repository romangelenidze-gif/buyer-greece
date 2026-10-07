<?php

namespace App\Enums;

enum QuoteStatus: string
{
    case DRAFT = 'draft';
    case SENT = 'sent';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case EXPIRED = 'expired';
    case SUPERSEDED = 'superseded';

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT => 'Черновик',
            self::SENT => 'Отправлен',
            self::ACCEPTED => 'Принят',
            self::REJECTED => 'Отклонён',
            self::EXPIRED => 'Истёк',
            self::SUPERSEDED => 'Заменён',
        };
    }
}