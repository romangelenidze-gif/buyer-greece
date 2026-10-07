<?php

namespace App\Filament\Resources;

use App\Enums\PackageSourceType;
use App\Enums\PackageStatus;
use App\Filament\Resources\PackageResource\Pages;
use App\Models\Package;
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

class PackageResource extends Resource
{
    protected static ?string $model = Package::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static \UnitEnum|string|null $navigationGroup = 'Логистика и Склад';

    protected static ?string $modelLabel = 'Входящая посылка';

    protected static ?string $pluralModelLabel = 'Посылки на складе';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основная информация')
                    ->schema([
                        Select::make('customer_id')
                            ->label('Владелец (Клиент)')
                            ->relationship('customer', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->first_name} {$record->last_name} ({$record->email})")
                            ->searchable()
                            ->required(),

                        Select::make('order_id')
                            ->label('Связанный заказ')
                            ->relationship('order', 'public_order_number')
                            ->searchable()
                            ->nullable(),

                        Select::make('source_type')
                            ->label('Тип поступления')
                            ->options(PackageSourceType::class)
                            ->default(PackageSourceType::SELF_PURCHASE)
                            ->required(),

                        Select::make('status')
                            ->label('Статус посылки')
                            ->options(PackageStatus::class)
                            ->default(PackageStatus::EXPECTED)
                            ->required(),
                    ])->columns(2),

                Section::make('Информация об отправлении')
                    ->schema([
                        TextInput::make('incoming_tracking_number')
                            ->label('Входящий трек-номер')
                            ->placeholder('1Z9999999999999999')
                            ->nullable(),

                        TextInput::make('store_name')
                            ->label('Магазин / Продавец')
                            ->placeholder('Amazon, Zara, eBay и т.д.')
                            ->nullable(),

                        Textarea::make('description')
                            ->label('Содержимое / Описание товаров')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Параметры и приемка')
                    ->schema([
                        TextInput::make('weight_kg')
                            ->label('Вес (кг)')
                            ->numeric()
                            ->default(0),

                        TextInput::make('dimensions')
                            ->label('Габариты (ДхШхВ см)')
                            ->placeholder('30x20x10')
                            ->nullable(),

                        TextInput::make('declared_value')
                            ->label('Декларируемая стоимость (€)')
                            ->numeric()
                            ->default(0),

                        DateTimePicker::make('received_at')
                            ->label('Дата и время получения на складе'),
                    ])->columns(2),

                Section::make('Дополнительно')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Складские примечания / замечания')
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

                Tables\Columns\TextColumn::make('incoming_tracking_number')
                    ->label('Трек-номер')
                    ->default('—')
                    ->searchable(),

                Tables\Columns\TextColumn::make('customer.first_name')
                    ->label('Клиент')
                    ->formatStateUsing(fn ($record) => $record->customer ? "{$record->customer->first_name} {$record->customer->last_name}" : '-')
                    ->searchable(),

                Tables\Columns\TextColumn::make('source_type')
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
                    ->money('EUR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('received_at')
                    ->label('Получено')
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
            'index' => Pages\ListPackages::route('/'),
            'create' => Pages\CreatePackage::route('/create'),
            'edit' => Pages\EditPackage::route('/{record}/edit'),
        ];
    }
}