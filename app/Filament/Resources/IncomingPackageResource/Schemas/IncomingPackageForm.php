<?php

namespace App\Filament\Resources\IncomingPackageResource\Schemas;

use App\Enums\PackageSourceType;
use App\Enums\PackageStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class IncomingPackageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('public_package_number')
                    ->label('Номер посылки')
                    ->required(),

                Select::make('customer_id')
                    ->label('Клиент')
                    ->relationship('customer', 'first_name')
                    ->searchable(['first_name', 'last_name', 'email'])
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->first_name} {$record->last_name} ({$record->email})")
                    ->nullable(),

                Select::make('order_id')
                    ->label('Заказ')
                    ->relationship('order', 'public_order_number')
                    ->searchable()
                    ->nullable(),

                Select::make('shipment_id')
                    ->label('Партия отправки')
                    ->relationship('shipment', 'shipment_number')
                    ->searchable()
                    ->nullable(),

                Select::make('source_type')
                    ->label('Источник')
                    ->options(PackageSourceType::class)
                    ->required(),

                TextInput::make('store_name')
                    ->label('Магазин'),

                TextInput::make('store_tracking_number')
                    ->label('Трек-номер магазина'),

                Textarea::make('description')
                    ->label('Описание')
                    ->columnSpanFull(),

                DatePicker::make('expected_date')
                    ->label('Ожидаемая дата'),

                DateTimePicker::make('received_at')
                    ->label('Дата получения'),

                TextInput::make('weight_kg')
                    ->label('Вес (кг)')
                    ->numeric(),

                TextInput::make('dimensions')
                    ->label('Габариты'),

                Select::make('status')
                    ->label('Статус')
                    ->options(PackageStatus::class)
                    ->required(),

                Textarea::make('photos_path')
                    ->label('Пути к фото')
                    ->columnSpanFull(),

                Textarea::make('internal_note')
                    ->label('Заметка')
                    ->columnSpanFull(),
            ]);
    }
}