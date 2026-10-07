<?php

namespace App\Filament\Resources;

use App\Models\AuditLog;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Журнал Аудита';
    protected static ?string $pluralModelLabel = 'Журнал Аудита';
    protected static ?string $modelLabel = 'Лог Аудита';

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Детали записи')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Дата и время')
                            ->dateTime('d.m.Y H:i:s'),

                        TextEntry::make('user.name')
                            ->label('Пользователь')
                            ->default('Система'),

                        TextEntry::make('action')
                            ->label('Действие')
                            ->columnSpanFull(),

                        TextEntry::make('auditable_type')
                            ->label('Сущность'),

                        TextEntry::make('auditable_id')
                            ->label('ID Объекта'),

                        TextEntry::make('details')
                            ->label('Подробные детали')
                            ->formatStateUsing(function ($state) {
                                if (is_array($state) || is_object($state)) {
                                    return json_encode($state, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                                }
                                return $state;
                            })
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата и время')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Пользователь')
                    ->default('Система'),

                Tables\Columns\TextColumn::make('action')
                    ->label('Действие')
                    ->searchable(),

                Tables\Columns\TextColumn::make('auditable_type')
                    ->label('Сущность'),

                Tables\Columns\TextColumn::make('auditable_id')
                    ->label('ID Объекта'),

                Tables\Columns\TextColumn::make('details')
                    ->label('Детали')
                    ->limit(30),
            ])
            ->actions([
                ViewAction::make()
                    ->label('Просмотр')
                    ->slideOver(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => AuditLogResource\Pages\ListAuditLogs::route('/'),
        ];
    }
}