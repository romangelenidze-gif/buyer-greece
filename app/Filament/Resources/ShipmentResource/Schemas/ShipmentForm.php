<?php

namespace App\Filament\Resources\ShipmentResource\Schemas;

use App\Enums\ShipmentStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ShipmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основная информация партии')
                    ->schema([
                        Select::make('customer_id')
                            ->label('Клиент')
                            ->relationship('customer', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->customer_number} — {$record->first_name} {$record->last_name}")
                            ->searchable()
                            ->required(),

                        TextInput::make('public_shipment_number')
                            ->label('Номер партии / Накладной')
                            ->default(fn () => 'SHP-' . strtoupper(substr(md5((string) microtime()), 0, 8)))
                            ->required()
                            ->unique(ignoreRecord: true),

                        Select::make('status')
                            ->label('Статус партии')
                            ->options(ShipmentStatus::class)
                            ->default(ShipmentStatus::PREPARING)
                            ->required(),

                        TextInput::make('carrier')
                            ->label('Перевозчик')
                            ->default('CAMEX')
                            ->required(),

                        TextInput::make('destination_country')
                            ->label('Страна назначения')
                            ->default('Грузия')
                            ->required(),
                    ])->columns(2),

                Section::make('Параметры и Camex Трекинг')
                    ->schema([
                        TextInput::make('weight_kg')
                            ->label('Общий вес (кг)')
                            ->numeric()
                            ->default(0.00),

                        TextInput::make('camex_tracking_number')
                            ->label('Трек-номер Camex')
                            ->maxLength(255),

                        TextInput::make('camex_status')
                            ->label('Статус Camex')
                            ->maxLength(255),

                        DateTimePicker::make('transferred_to_camex_at')
                            ->label('Дата передачи в Camex')
                            ->disabled(),
                    ])->columns(2),

                Section::make('Дополнительно')
                    ->schema([
                        Textarea::make('internal_note')
                            ->label('Внутренняя заметка')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}