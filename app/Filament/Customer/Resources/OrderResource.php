<?php

namespace App\Filament\Customer\Resources;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Filament\Customer\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\ViewAction;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Мои заказы';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('customer_id', auth()->user()->customer_id);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Hidden::make('customer_id')
                    ->default(fn () => auth()->user()->customer_id),
                
                Forms\Components\Hidden::make('type')
                    ->default(OrderType::BUY_FOR_ME->value),

                Forms\Components\Hidden::make('status')
                    ->default(OrderStatus::UNDER_REVIEW->value),

                Forms\Components\Hidden::make('submitted_at')
                    ->default(now()),

                Forms\Components\Textarea::make('internal_note')
                    ->label('Комментарий к заказу')
                    ->placeholder('Дополнительные пожелания по доставке или выкупу'),

                Forms\Components\Repeater::make('items')
                    ->label('Товары для выкупа')
                    ->relationship()
                    ->schema([
                        Forms\Components\TextInput::make('product_name')
                            ->label('Название товара')
                            ->required(),
                        Forms\Components\TextInput::make('product_url')
                            ->label('Ссылка на товар')
                            ->url()
                            ->required(),
                        Forms\Components\TextInput::make('quantity')
                            ->label('Количество')
                            ->numeric()
                            ->default(1)
                            ->required(),
                        Forms\Components\TextInput::make('size')
                            ->label('Размер'),
                        Forms\Components\TextInput::make('color')
                            ->label('Цвет'),
                        Forms\Components\Textarea::make('requested_comment')
                            ->label('Примечание к позиции'),
                    ])
                    ->minItems(1)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('public_order_number')
                    ->label('Номер заказа')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Тип')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->badge(),
                Tables\Columns\TextColumn::make('activeQuote.total')
                    ->label('Сумма сметы')
                    ->money('EUR'),
                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Дата оформления')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                \Filament\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }
}