<?php

namespace App\Filament\Resources\ShipmentResource\RelationManagers;

use App\Enums\PackageStatus;
use Filament\Actions\AssociateAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class PackagesRelationManager extends RelationManager
{
    protected static string $relationship = 'packages';

    protected static ?string $title = 'Посылки в этой партии';

    protected static ?string $modelLabel = 'Посылка';

    protected static ?string $pluralModelLabel = 'Посылки';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('incoming_tracking_number')
                    ->label('Трек-номер')
                    ->disabled(),

                Select::make('status')
                    ->label('Статус посылки')
                    ->options(PackageStatus::class)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('incoming_tracking_number')
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
                    ->formatStateUsing(fn ($record) => $record->customer ? "{$record->customer->first_name} {$record->customer->last_name}" : '-'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->badge(),

                Tables\Columns\TextColumn::make('weight_kg')
                    ->label('Вес (кг)')
                    ->sortable(),

                Tables\Columns\TextColumn::make('declared_value')
                    ->label('Ценность')
                    ->money('EUR'),
            ])
            ->headerActions([
                AssociateAction::make()
                    ->label('Добавить посылку в партию')
                    ->preloadRecordSelect(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DissociateAction::make()
                    ->label('Убрать из партии'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    DissociateBulkAction::make()
                        ->label('Убрать выбранные'),
                ]),
            ]);
    }
}