<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Enums\PackageStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Package;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // 1. Активные заказы (не завершённые и не отменённые)
        $activeOrdersCount = Order::whereNotIn('status', [
            OrderStatus::COMPLETED,
            OrderStatus::CANCELLED,
            OrderStatus::REJECTED,
        ])->count();

        // 2. Сумма платежей, ожидающих проверки администратора
        $pendingPaymentsSum = Payment::where('status', PaymentStatus::PENDING)->sum('amount');

        // 3. Посылки, находящиеся прямо сейчас на складе в Греции
        $warehousePackagesCount = Package::where('status', PackageStatus::RECEIVED_IN_GREECE)->count();

        return [
            Stat::make('Активные заказы', $activeOrdersCount)
                ->description('Находятся в процессе выкупа/обработки')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary'),

            Stat::make('Платежи на проверке', '€' . number_format($pendingPaymentsSum, 2, '.', ' '))
                ->description('Требуют подтверждения в системе')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),

            Stat::make('На складе в Греции', $warehousePackagesCount)
                ->description('Готовы к формированию партий')
                ->descriptionIcon('heroicon-m-cube')
                ->color('success'),
        ];
    }
}