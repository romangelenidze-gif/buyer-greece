<?php

namespace App\Services;

/**
 * Конвертация бюджета пользователя в EUR (BestPrice работает только в евро).
 * Курсы ориентировочные: переопределяются в config/services.php -> 'currency_rates'.
 */
class CurrencyConverter
{
    protected const DEFAULT_RATES = ['EUR' => 1.0, 'USD' => 0.92, 'RUB' => 0.010, 'GEL' => 0.34];

    public static function rate(string $currency): ?float
    {
        $rates = array_merge(self::DEFAULT_RATES, (array) config('services.currency_rates', []));
        $currency = strtoupper($currency);

        return isset($rates[$currency]) && is_numeric($rates[$currency]) ? (float) $rates[$currency] : null;
    }

    public static function toEur(float $amount, string $currency): ?float
    {
        $rate = self::rate($currency);

        return $rate === null ? null : round($amount * $rate, 2);
    }
}
