<?php

namespace App\Filament\Resources\OrderResource\Tables;

use App\Actions\Quotes\CreateQuoteAction;
use App\Enums\OrderStatus;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('public_order_number')
                    ->label('Номер заказа')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('customer.first_name')
                    ->label('Клиент')
                    ->formatStateUsing(fn ($record) => $record->customer ? "{$record->customer->first_name} {$record->customer->last_name}" : '-')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Тип')
                    ->badge(),

                TextColumn::make('status')
                    ->label('Статус')
                    ->badge(),

                TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Action::make('createQuote')
                    ->label('Создать расчёт')
                    ->icon('heroicon-o-calculator')
                    ->color('primary')
                    ->visible(function (Order $record) {
                        $status = $record->status instanceof \BackedEnum ? $record->status->value : (string) $record->status;
                        return in_array($status, ['under_review', 'quote_expired', 'quote_prepared', 'pending']);
                    })
                    ->form([
                        TextInput::make('product_total')
                            ->label('Стоимость товаров (€)')
                            ->numeric()
                            ->required(),

                        TextInput::make('local_shipping')
                            ->label('Доставка по Греции (€)')
                            ->numeric()
                            ->default(0.00)
                            ->required(),

                        TextInput::make('buyer_fee')
                            ->label('Комиссия байера (€)')
                            ->numeric()
                            ->required(),

                        TextInput::make('services_total')
                            ->label('Дополнительные услуги (€)')
                            ->numeric()
                            ->default(0.00)
                            ->required(),

                        TextInput::make('other_costs')
                            ->label('Прочие расходы (€)')
                            ->numeric()
                            ->default(0.00)
                            ->required(),

                        TextInput::make('discount')
                            ->label('Скидка (€)')
                            ->numeric()
                            ->default(0.00)
                            ->required(),

                        Textarea::make('notes')
                            ->label('Примечания к расчёту')
                            ->columnSpanFull(),
                    ])
                    ->action(function (Order $record, array $data, CreateQuoteAction $createQuoteAction) {
                        $productTotal = (float) ($data['product_total'] ?? 0);
                        $localShipping = (float) ($data['local_shipping'] ?? 0);
                        $buyerFee = (float) ($data['buyer_fee'] ?? 0);
                        $servicesTotal = (float) ($data['services_total'] ?? 0);
                        $otherCosts = (float) ($data['other_costs'] ?? 0);
                        $discount = (float) ($data['discount'] ?? 0);

                        $total = ($productTotal + $localShipping + $buyerFee + $servicesTotal + $otherCosts) - $discount;

                        $quoteData = [
                            'product_total' => $productTotal,
                            'local_shipping' => $localShipping,
                            'buyer_fee' => $buyerFee,
                            'services_total' => $servicesTotal,
                            'other_costs' => $otherCosts,
                            'discount' => $discount,
                            'total' => $total,
                            'notes' => $data['notes'] ?? null,
                        ];

                        $createQuoteAction->execute(
                            order: $record,
                            quoteData: $quoteData,
                            managerUserId: (int) auth()->id()
                        );

                        Notification::make()
                            ->title('Коммерческий расчёт (Quote) сформирован')
                            ->success()
                            ->send();
                    }),

                Action::make('markAsPurchased')
                    ->label('Выкуплен')
                    ->icon('heroicon-o-shopping-cart')
                    ->color('success')
                    ->visible(function (Order $record) {
                        $status = $record->status instanceof \BackedEnum ? $record->status->value : (string) $record->status;
                        return in_array($status, ['paid', 'processing', 'quote_approved', 'approved']);
                    })
                    ->form([
                        TextInput::make('store_tracking_number')
                            ->label('Трек-номер / Заказ в магазине'),
                        Textarea::make('purchase_note')
                            ->label('Заметка по выкупу'),
                    ])
                    ->action(function (Order $record, array $data) {
                        $note = "Выкуплен менеджером.";
                        if (!empty($data['store_tracking_number'])) {
                            $note .= " Трек магазина: {$data['store_tracking_number']}.";
                        }
                        if (!empty($data['purchase_note'])) {
                            $note .= " {$data['purchase_note']}";
                        }

                        $purchasedStatus = defined('App\Enums\OrderStatus::PURCHASED') ? OrderStatus::PURCHASED : 'purchased';

                        $record->update([
                            'status' => $purchasedStatus,
                            'internal_note' => trim(($record->internal_note ?? '') . "\n" . $note),
                        ]);

                        Notification::make()
                            ->title('Заказ отмечен как выкупленный')
                            ->success()
                            ->send();
                    }),

                Action::make('downloadPdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->action(function (Order $record) {
                        $pdf = Pdf::loadView('pdf.order-invoice', ['order' => $record]);

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            "invoice-{$record->public_order_number}.pdf"
                        );
                    }),

                Action::make('cancelOrder')
                    ->label('Отменить')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(function (Order $record) {
                        $status = $record->status instanceof \BackedEnum ? $record->status->value : (string) $record->status;
                        return ! in_array($status, ['cancelled', 'delivered']);
                    })
                    ->form([
                        Textarea::make('cancellation_reason')
                            ->label('Причина отмены заказа')
                            ->required(),
                    ])
                    ->action(function (Order $record, array $data) {
                        $cancelledStatus = defined('App\Enums\OrderStatus::CANCELLED') ? OrderStatus::CANCELLED : 'cancelled';

                        $record->update([
                            'status' => $cancelledStatus,
                            'internal_note' => trim(($record->internal_note ?? '') . "\nОтменён: " . $data['cancellation_reason']),
                        ]);

                        Notification::make()
                            ->title('Заказ отменён')
                            ->warning()
                            ->send();
                    }),

                EditAction::make(),
                ViewAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}