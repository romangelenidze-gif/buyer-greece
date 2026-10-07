<?php

namespace App\Filament\Resources;

use App\Enums\QuoteStatus;
use App\Filament\Resources\QuoteResource\Pages;
use App\Models\Quote;
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

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    protected static \UnitEnum|string|null $navigationGroup = 'Финансы и Расчёты';

    protected static ?string $modelLabel = 'Коммерческий расчёт';

    protected static ?string $pluralModelLabel = 'Коммерческие расчёты';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основная информация')
                    ->schema([
                        Select::make('order_id')
                            ->label('Заказ')
                            ->relationship('order', 'public_order_number')
                            ->searchable()
                            ->required(),
                        Select::make('status')
                            ->label('Статус')
                            ->options(QuoteStatus::class)
                            ->default(QuoteStatus::DRAFT)
                            ->required(),
                        DateTimePicker::make('valid_until')
                            ->label('Действителен до')
                            ->default(now()->addHours(48))
                            ->required(),
                        TextInput::make('currency')
                            ->label('Валюта')
                            ->default('EUR')
                            ->disabled()
                            ->dehydrated(),
                    ])->columns(2),

                Section::make('Детализация стоимости (€)')
                    ->schema([
                        TextInput::make('product_total')
                            ->label('Стоимость товаров (€)')
                            ->numeric()
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::recalculateTotal($set, $get)),
                        TextInput::make('local_shipping')
                            ->label('Доставка по Греции (€)')
                            ->numeric()
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::recalculateTotal($set, $get)),
                        TextInput::make('buyer_fee')
                            ->label('Комиссия выкупа (€)')
                            ->numeric()
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::recalculateTotal($set, $get)),
                        TextInput::make('services_total')
                            ->label('Доп. услуги (€)')
                            ->numeric()
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::recalculateTotal($set, $get)),
                        TextInput::make('other_costs')
                            ->label('Прочие расходы (€)')
                            ->numeric()
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::recalculateTotal($set, $get)),
                        TextInput::make('discount')
                            ->label('Скидка (€)')
                            ->numeric()
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::recalculateTotal($set, $get)),
                        TextInput::make('total')
                            ->label('Итоговая сумма (€)')
                            ->numeric()
                            ->readOnly()
                            ->dehydrated()
                            ->columnSpanFull(),
                    ])->columns(3),

                Section::make('Дополнительно')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Примечания / условия')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected static function recalculateTotal(callable $set, callable $get): void
    {
        $product = floatval($get('product_total') ?? 0);
        $localShipping = floatval($get('local_shipping') ?? 0);
        $buyerFee = floatval($get('buyer_fee') ?? 0);
        $services = floatval($get('services_total') ?? 0);
        $other = floatval($get('other_costs') ?? 0);
        $discount = floatval($get('discount') ?? 0);

        $total = ($product + $localShipping + $buyerFee + $services + $other) - $discount;

        $set('total', number_format(max(0, $total), 2, '.', ''));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order.public_order_number')
                    ->label('№ Заказа')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order.customer.first_name')
                    ->label('Клиент')
                    ->formatStateUsing(fn ($record) => $record->order?->customer ? "{$record->order->customer->first_name} {$record->order->customer->last_name}" : '-')
                    ->searchable(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label('Создал')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->badge(),
                Tables\Columns\TextColumn::make('product_total')
                    ->label('Товары (€)')
                    ->money('EUR'),
                Tables\Columns\TextColumn::make('total')
                    ->label('Итого (€)')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('valid_until')
                    ->label('Действителен до')
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
            'index' => Pages\ListQuotes::route('/'),
            'create' => Pages\CreateQuote::route('/create'),
            'edit' => Pages\EditQuote::route('/{record}/edit'),
        ];
    }
}