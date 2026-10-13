<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\Tables\OrdersTable;
use App\Models\Order;
use App\Models\OrderItem;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static \UnitEnum|string|null $navigationGroup = 'Заказы и Клиенты';

    protected static ?string $modelLabel = 'Заказ';

    protected static ?string $pluralModelLabel = 'Заказы';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основная информация')
                    ->schema([
                        TextInput::make('public_order_number')
                            ->label('Номер заказа')
                            ->disabled()
                            ->dehydrated(false),

                        Select::make('customer_id')
                            ->label('Клиент')
                            ->relationship('customer', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->customer_number} — {$record->first_name} {$record->last_name}")
                            ->searchable()
                            ->required(),

                        Select::make('type')
                            ->label('Тип заказа')
                            ->options([
                                'buy_for_me' => 'Купить за меня',
                                'manual_order' => 'Заказ через менеджера',
                            ])
                            ->required(),

                        Select::make('status')
                            ->label('Статус')
                            ->options(OrderStatus::class)
                            ->required(),

                        Textarea::make('internal_note')
                            ->label('Внутренняя заметка менеджера')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Товары в заказе')
                    ->schema([
                        Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Hidden::make('screenshot_path'),

                                TextInput::make('product_name')
                                    ->label('Название товара')
                                    ->required(),

                                TextInput::make('product_url')
                                    ->label('Ссылка на товар')
                                    ->url()
                                    ->columnSpanFull(),

                                TextInput::make('quantity')
                                    ->label('Количество')
                                    ->numeric()
                                    ->default(1)
                                    ->required(),

                                TextInput::make('size')->label('Размер'),
                                TextInput::make('color')->label('Цвет'),
                                TextInput::make('model')->label('Модель'),

                                TextInput::make('unit_price')
                                    ->label('Цена за ед. (€)')
                                    ->numeric(),

                                Textarea::make('requested_comment')
                                    ->label('Комментарий клиента')
                                    ->columnSpanFull(),

                                Textarea::make('admin_comment')
                                    ->label('Заметка менеджера по товару')
                                    ->columnSpanFull(),

                                Placeholder::make('screenshot_preview_box')
                                    ->label('Скриншот клиента')
                                    ->content(function ($get, ?OrderItem $record) {
                                        $path = $get('screenshot_path') ?? $record?->screenshot_path;
                                        $itemId = $record?->id;
                                        $orderId = $record?->order_id;

                                        if (! $path) {
                                            return new HtmlString('<span class="text-gray-400 text-sm">Скриншот не прикреплен к этому товару</span>');
                                        }

                                        $url = $record?->screenshot_url;

                                        if (! $url && $orderId && $itemId) {
                                            $url = route('orders.items.screenshot', [$orderId, $itemId]);
                                        }

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
        return OrdersTable::configure($table);
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