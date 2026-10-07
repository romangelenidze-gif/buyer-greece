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
                        TextInput::make('shipment_number')
                            ->label('Номер партии / Накладной')
                            ->default(fn () => 'SHP-' . strtoupper(substr(md5((string) time()), 0, 8)))
                            ->required()
                            ->unique(ignoreRecord: true),

                        Select::make('status')
                            ->label('Статус партии')
                            ->options(ShipmentStatus::class)
                            ->default(ShipmentStatus::PREPARING)
                            ->required(),

                        TextInput::make('carrier')
                            ->label('Перевозчик / Служба')
                            ->default('CAMEX')
                            ->required(),

                        TextInput::make('destination_country')
                            ->label('Страна назначения')
                            ->placeholder('Грузия, Армения и т.д.')
                            ->nullable(),
                    ])->columns(2),

                Section::make('Параметры и стоимость')
                    ->schema([
                        TextInput::make('total_weight_kg')
                            ->label('Общий вес (кг)')
                            ->numeric()
                            ->default(0),

                        TextInput::make('total_cost')
                            ->label('Общая стоимость (€)')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),

                Section::make('Даты отправки и логистики')
                    ->schema([
                        DateTimePicker::make('dispatched_at')
                            ->label('Дата передачи перевозчику'),

                        DateTimePicker::make('delivered_at')
                            ->label('Дата завершения / доставки'),
                    ])->columns(2),

                Section::make('Дополнительно')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Заметки к партии')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}