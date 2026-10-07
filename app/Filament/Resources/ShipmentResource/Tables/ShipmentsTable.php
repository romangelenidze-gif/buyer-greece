<?php

namespace App\Filament\Resources\ShipmentResource\Tables;

use App\Actions\Shipments\TransferToCamexAction;
use App\Enums\ShipmentStatus;
use App\Models\Shipment;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;

class ShipmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('public_shipment_number')
                    ->label('№ Партии')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('carrier')
                    ->label('Перевозчик')
                    ->searchable(),

                Tables\Columns\TextColumn::make('destination_country')
                    ->label('Направление')
                    ->default('—'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->badge(),

                Tables\Columns\TextColumn::make('packages_count')
                    ->label('Посылок')
                    ->counts('packages')
                    ->sortable(),

                Tables\Columns\TextColumn::make('weight_kg')
                    ->label('Вес (кг)')
                    ->sortable(),

                Tables\Columns\TextColumn::make('transferred_to_camex_at')
                    ->label('Отправлено в Camex')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Action::make('transferToCamex')
                    ->label('Передать в Camex')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('primary')
                    ->visible(fn (Shipment $record) => $record->status !== ShipmentStatus::TRANSFERRED_TO_CAMEX && $record->status?->value !== 'transferred_to_camex')
                    ->form([
                        TextInput::make('camex_tracking_number')
                            ->label('Трек-номер Camex')
                            ->required(),
                        TextInput::make('camex_status')
                            ->label('Начальный статус')
                            ->default('registered'),
                    ])
                    ->action(function (Shipment $record, array $data, TransferToCamexAction $transferToCamexAction) {
                        $transferToCamexAction->execute(
                            shipment: $record,
                            camexTrackingNumber: $data['camex_tracking_number'],
                            camexStatus: $data['camex_status'] ?? null,
                            managerUserId: (int) auth()->id()
                        );

                        Notification::make()
                            ->title('Отправление передано в Camex')
                            ->success()
                            ->send();
                    }),

                Action::make('updateStatus')
                    ->label('Статус')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->form([
                        Select::make('status')
                            ->label('Новый статус партии')
                            ->options(ShipmentStatus::class)
                            ->required(),
                    ])
                    ->action(function (Shipment $record, array $data) {
                        $record->update([
                            'status' => $data['status'],
                        ]);

                        Notification::make()
                            ->title('Статус партии успешно обновлён')
                            ->success()
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