<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class AuditLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static \UnitEnum|string|null $navigationGroup = 'Система';

    protected static ?string $modelLabel = 'Лог аудита';

    protected static ?string $pluralModelLabel = 'Журнал аудита';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Дата и время')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable(),

                TextColumn::make('causer.name')
                    ->label('Пользователь')
                    ->default('Система / Гость')
                    ->searchable(),

                TextColumn::make('description')
                    ->label('Действие')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('subject_type')
                    ->label('Сущность')
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->sortable(),

                TextColumn::make('subject_id')
                    ->label('ID Объекта'),

                TextColumn::make('properties')
                    ->label('Детали')
                    ->formatStateUsing(fn ($state) => json_encode($state, JSON_UNESCAPED_UNICODE))
                    ->limit(50),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
            'view' => Pages\ViewAuditLog::route('/{record}'),
        ];
    }
}