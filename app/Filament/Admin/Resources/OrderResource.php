<?php

namespace App\Filament\Admin\Resources;

use App\Enums\OrderStatus;
use App\Filament\Admin\Resources\OrderResource\Pages;
use App\Models\Order;
use BackedEnum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use UnitEnum;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static string|UnitEnum|null $navigationGroup = 'Заказы и Клиенты';

    protected static ?string $modelLabel = 'Заказ';

    protected static ?string $pluralModelLabel = 'Заказы';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\TextInput::make('public_order_number')
                            ->label('Номер заказа')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\Select::make('customer_id')
                            ->label('Клиент')
                            ->relationship('customer', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->customer_number} — {$record->first_name} {$record->last_name}")
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('type')
                            ->label('Тип заказа')
                            ->options([
                                'buy_for_me' => 'Купить за меня',
                                'manual_order' => 'Заказ через менеджера',
                            ])
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Статус')
                            ->options(OrderStatus::class)
                            ->required(),
                        Forms\Components\Textarea::make('internal_note')
                            ->label('Внутренняя заметка менеджера')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Товары в заказе')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                // Обязательно объявляем поле в схеме, чтобы Filament загружал его из БД
                                Forms\Components\Hidden::make('screenshot_path'),

                                Forms\Components\TextInput::make('product_name')
                                    ->label('Название товара')
                                    ->required(),
                                Forms\Components\TextInput::make('product_url')
                                    ->label('Ссылка на товар')
                                    ->url()
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('quantity')
                                    ->label('Количество')
                                    ->numeric()
                                    ->default(1)
                                    ->required(),
                                Forms\Components\TextInput::make('size')->label('Размер'),
                                Forms\Components\TextInput::make('color')->label('Цвет'),
                                Forms\Components\TextInput::make('model')->label('Модель'),
                                Forms\Components\TextInput::make('unit_price')
                                    ->label('Цена за ед. (€)')
                                    ->numeric(),
                                Forms\Components\Textarea::make('requested_comment')
                                    ->label('Комментарий клиента')
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('admin_comment')
                                    ->label('Заметка менеджера по товару')
                                    ->columnSpanFull(),

                                // Надежный вывод превью и кнопки через состояние строки репитера
                                Forms\Components\Placeholder::make('screenshot_preview_box')
                                    ->label('Скриншот клиента')
                                    ->content(function (Forms\Get $get, ?Order $record) {
                                        $path = $get('screenshot_path');
                                        $itemId = $get('id');
                                        $orderId = $record?->id;

                                        if (! $path) {
                                            return new HtmlString('<span class="text-gray-400 text-sm">Скриншот не прикреплен к этому товару</span>');
                                        }

                                        $url = ($orderId && $itemId) 
                                            ? route('orders.items.screenshot', [$orderId, $itemId]) 
                                            : null;

                                        if (! $url) {
                                            return new HtmlString('<span class="text-gray-500 text-sm">Путь файла: ' . e($path) . '</span>');
                                        }

                                        return new HtmlString(
                                            '<div class="mt-2 flex flex-col gap-3">' .
                                            '<div>' .
                                            '<a href="' . $url . '" target="_blank">' .
                                            '<img src="' . $url . '" alt="Скриншот" class="w-32 h-32 object-cover rounded-lg border shadow-sm hover:opacity-95 transition" />' .
                                            '</a>' .
                                            '</div>' .
                                            '<div>' .
                                            '<a href="' . $url . '" target="_blank" class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-white bg-primary-600 rounded-md hover:bg-primary-500 shadow transition">' .
                                            'Открыть в полном размере / Скачать' .
                                            '</a>' .
                                            '</div>' .
                                            '</div>'
                                        );
                                    })
                                    ->columnSpanFull(),
                            ])
                            ->columns(3)
                            ->defaultItems(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('public_order_number')
                    ->label('Номер заказа')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.first_name')
                    ->label('Клиент')
                    ->formatStateUsing(fn ($record) => $record->customer ? "{$record->customer->first_name} {$record->customer->last_name}" : '-')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Тип')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}