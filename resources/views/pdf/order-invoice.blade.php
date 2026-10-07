<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Инвойс № {{ $order->public_order_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; text-transform: uppercase; }
        .info-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .info-table td { padding: 5px; vertical-align: top; }
        .items-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .items-table th, .items-table td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .items-table th { background-color: #f2f2f2; font-weight: bold; }
        .total-box { margin-top: 20px; text-align: right; font-size: 14px; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <div class="title">Коммерческий Инвойс / Сопроводительная накладная</div>
        <div>Логистика: Греция &rarr; Грузия</div>
    </div>

    <table class="info-table">
        <tr>
            <td>
                <strong>Отправитель:</strong><br>
                Buyer Greece Warehouse<br>
                Афины, Греция<br>
                Email: support@buyer-greece.com
            </td>
            <td>
                <strong>Получатель:</strong><br>
                {{ $order->customer?->first_name }} {{ $order->customer?->last_name }}<br>
                Email: {{ $order->customer?->email }}<br>
                Телефон: {{ $order->customer?->phone ?? '—' }}
            </td>
            <td>
                <strong>Заказ №:</strong> {{ $order->public_order_number }}<br>
                <strong>Дата:</strong> {{ $order->created_at->format('d.m.Y') }}<br>
                <strong>Статус:</strong> {{ $order->status->getLabel() }}
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>№</th>
                <th>Наименование товара / Описание</th>
                <th>Кол-во</th>
                <th>Цена (€)</th>
                <th>Сумма (€)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product_name ?? 'Товар из заказа' }}</td>
                    <td>{{ $item->quantity ?? 1 }}</td>
                    <td>€{{ number_format($item->price ?? 0, 2) }}</td>
                    <td>€{{ number_format(($item->quantity ?? 1) * ($item->price ?? 0), 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Нет позиций в заказе</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="total-box">
        Итоговая стоимость: €{{ number_format($order->quote?->total ?? $order->total_amount ?? 0, 2) }}
    </div>

</body>
</html>