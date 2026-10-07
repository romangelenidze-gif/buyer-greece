<?php

namespace App\Filament\Resources\ShipmentResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;

class ShipmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('shipment_number')
                    ->label('№ Партии')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('carrier')
                    ->label('Перевозчик')
                    ->searchable(),

                Tables\Columns\TextColumn::make('destination_country')
                    ->label('Направление')
                    ->default('—'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->badge(),

                Tables\Columns\TextColumn::make('packages_count')
                    ->label('Посылок')
                    ->counts('packages')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_weight_kg')
                    ->label('Вес (кг)')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_cost')
                    ->label('Стоимость')
                    ->money('EUR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('dispatched_at')
                    ->label('Отправлено')
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
}