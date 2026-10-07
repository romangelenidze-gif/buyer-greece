<?php

namespace App\Filament\Resources\IncomingPackageResource\Tables;

use App\Enums\PackageStatus;
use App\Models\IncomingPackage;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;

class IncomingPackagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('incoming_tracking_number')
                    ->label('Трек-номер')
                    ->searchable()
                    ->default('—'),

                Tables\Columns\TextColumn::make('customer.first_name')
                    ->label('Клиент')
                    ->formatStateUsing(fn ($record) => $record->customer ? "{$record->customer->first_name} {$record->customer->last_name}" : 'Не привязан')
                    ->searchable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Тип')
                    ->badge(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->badge(),

                Tables\Columns\TextColumn::make('weight_kg')
                    ->label('Вес (кг)')
                    ->sortable(),

                Tables\Columns\TextColumn::make('declared_value')
                    ->label('Ценность')
                    ->money('EUR'),

                Tables\Columns\TextColumn::make('received_at')
                    ->label('Получено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->actions([
                // Принять на склад в Греции
                Action::make('receivePackage')
                    ->label('Принять на склад')
                    ->icon('heroicon-o-cube')
                    ->color('success')
                    ->visible(function (IncomingPackage $record) {
                        $status = $record->status instanceof \BackedEnum ? $record->status->value : (string) $record->status;
                        return in_array($status, [
                            PackageStatus::EXPECTED->value,
                            PackageStatus::UNIDENTIFIED->value,
                        ]);
                    })
                    ->form([
                        TextInput::make('weight_kg')
                            ->label('Фактический вес (кг)')
                            ->numeric()
                            ->required(),
                        TextInput::make('declared_value')
                            ->label('Объявленная ценность (€)')
                            ->numeric(),
                        Textarea::make('admin_note')
                            ->label('Заметка о состоянии посылки / упаковке'),
                    ])
                    ->action(function (IncomingPackage $record, array $data) {
                        $record->update([
                            'weight_kg' => $data['weight_kg'],
                            'declared_value' => $data['declared_value'] ?? $record->declared_value,
                            'status' => PackageStatus::RECEIVED_IN_GREECE,
                            'received_at' => now(),
                            'admin_note' => $data['admin_note'] ?? $record->admin_note,
                        ]);

                        Notification::make()
                            ->title('Посылка принята на склад в Греции')
                            ->success()
                            ->send();
                    }),

                // Быстрая смена статуса
                Action::make('changeStatus')
                    ->label('Статус')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->form([
                        Select::make('status')
                            ->label('Новый статус посылки')
                            ->options(PackageStatus::class)
                            ->required(),
                    ])
                    ->action(function (IncomingPackage $record, array $data) {
                        $record->update([
                            'status' => $data['status'],
                        ]);

                        Notification::make()
                            ->title('Статус посылки изменён')
                            ->success()
                            ->send();
                    }),

                // Привязать клиента (доступно, если клиент не заполнен)
                Action::make('assignCustomer')
                    ->label('Привязать клиента')
                    ->icon('heroicon-o-user-plus')
                    ->color('primary')
                    ->visible(fn (IncomingPackage $record) => is_null($record->customer_id))
                    ->form([
                        Select::make('customer_id')
                            ->label('Клиент')
                            ->relationship('customer', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->customer_number} — {$record->first_name} {$record->last_name}")
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (IncomingPackage $record, array $data) {
                        $record->update([
                            'customer_id' => $data['customer_id'],
                        ]);

                        Notification::make()
                            ->title('Клиент привязан к посылке')
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