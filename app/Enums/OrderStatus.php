<?php

namespace App\Enums;

enum OrderStatus: string
{
    case NEW = 'new';
    case DRAFT = 'draft';
    case UNDER_REVIEW = 'under_review';
    case QUOTE_PREPARED = 'quote_prepared';
    case QUOTE_EXPIRED = 'quote_expired';
    case AWAITING_PAYMENT = 'awaiting_payment';
    case PAID = 'paid';
    case PURCHASING = 'purchasing';
    case PURCHASED = 'purchased';
    case RECEIVED_IN_GREECE = 'received_in_greece';
    case READY_FOR_SHIPMENT = 'ready_for_shipment';
    case COMPLETED = 'completed';
    case REFUND_REQUIRED = 'refund_required';
    case REFUNDED = 'refunded';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case APPROVED = 'approved';

    public function getLabel(): string
    {
        return match ($this) {
            self::NEW => 'Новый',
            self::DRAFT => 'Черновик',
            self::UNDER_REVIEW => 'На проверке',
            self::QUOTE_PREPARED => 'Расчёт подготовлен',
            self::QUOTE_EXPIRED => 'Срок расчёта истёк',
            self::AWAITING_PAYMENT => 'Ожидает оплаты',
            self::PAID => 'Оплачен',
            self::PURCHASING => 'В процессе покупки',
            self::PURCHASED => 'Куплен',
            self::RECEIVED_IN_GREECE => 'Получен в Греции',
            self::READY_FOR_SHIPMENT => 'Готов к отправке',
            self::COMPLETED => 'Завершён',
            self::REFUND_REQUIRED => 'Требуется возврат',
            self::REFUNDED => 'Возвращён',
            self::REJECTED => 'Отклонён',
            self::CANCELLED => 'Отменён',
            self::PENDING => 'Ожидает',
            self::PROCESSING => 'В обработке',
            self::APPROVED => 'Одобрен',
        };
    }
}