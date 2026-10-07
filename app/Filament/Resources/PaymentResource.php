<?php

namespace App\Filament\Resources;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static \UnitEnum|string|null $navigationGroup = 'Финансы и Расчёты';

    protected static ?string $modelLabel = 'Платёж';

    protected static ?string $pluralModelLabel = 'Платежи';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Информация о платеже')
                    ->schema([
                        Select::make('customer_id')
                            ->label('Клиент')
                            ->relationship('customer', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->first_name} {$record->last_name} ({$record->email})")
                            ->searchable()
                            ->required(),

                        Select::make('order_id')
                            ->label('Заказ')
                            ->relationship('order', 'public_order_number')
                            ->searchable()
                            ->nullable(),

                        Select::make('quote_id')
                            ->label('Коммерческий расчёт')
                            ->relationship('quote', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "Расчёт #{$record->id} (€{$record->total})")
                            ->searchable()
                            ->nullable(),

                        Select::make('status')
                            ->label('Статус платежа')
                            ->options(PaymentStatus::class)
                            ->default(PaymentStatus::PENDING)
                            ->required(),
                    ])->columns(2),

                Section::make('Сумма и транзакция')
                    ->schema([
                        TextInput::make('amount')
                            ->label('Сумма (€)')
                            ->numeric()
                            ->required(),

                        TextInput::make('currency')
                            ->label('Валюта')
                            ->default('EUR')
                            ->disabled()
                            ->dehydrated(),

                        Select::make('payment_method')
                            ->label('Способ оплаты')
                            ->options(PaymentMethod::class)
                            ->required(),

                        TextInput::make('transaction_id')
                            ->label('ID транзакции / Чек')
                            ->nullable(),

                        DateTimePicker::make('paid_at')
                            ->label('Дата и время оплаты')
                            ->default(now()),
                    ])->columns(2),

                Section::make('Дополнительно')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Примечания к платежу')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.first_name')
                    ->label('Клиент')
                    ->formatStateUsing(fn ($record) => $record->customer ? "{$record->customer->first_name} {$record->customer->last_name}" : '-')
                    ->searchable(),

                Tables\Columns\TextColumn::make('order.public_order_number')
                    ->label('Заказ')
                    ->default('-')
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Сумма')
                    ->money('EUR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Способ оплаты')
                    ->badge(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->badge(),

                Tables\Columns\TextColumn::make('transaction_id')
                    ->label('Транзакция')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Оплачено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->actions([
                EditAction::make(),
                ViewAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}