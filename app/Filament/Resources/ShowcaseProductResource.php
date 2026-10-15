<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShowcaseProductResource\Pages;
use App\Models\ShowcaseProduct;
use Filament\Actions\DeleteAction; // Корректное пространство имен
use Filament\Actions\EditAction;   // Корректное пространство имен
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ShowcaseProductResource extends Resource
{
    protected static ?string $model = ShowcaseProduct::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';
    protected static \UnitEnum|string|null $navigationGroup = 'Каталог и Витрина';
    protected static ?string $modelLabel = 'Товар витрины';
    protected static ?string $pluralModelLabel = 'Популярные товары';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основная информация')
                    ->schema([
                        TextInput::make('name')
                            ->label('Название товара')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('partner_name')
                            ->label('Магазин / Партнер')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('partner_product_url')
                            ->label('Ссылка на оригинальный товар')
                            ->url()
                            ->required()
                            ->columnSpanFull(),

                        FileUpload::make('image_path')
                            ->label('Изображение товара')
                            ->image()
                            ->disk('public')
                            ->directory('showcase')
                            ->visibility('public')
                            ->required()
                            ->columnSpanFull(),

                        Textarea::make('short_description')
                            ->label('Короткое описание')
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Цены и отображение')
                    ->schema([
                        TextInput::make('price')
                            ->label('Текущая цена (€)')
                            ->numeric()
                            ->prefix('€'),

                        TextInput::make('old_price')
                            ->label('Прежняя цена (€)')
                            ->numeric()
                            ->prefix('€'),

                        TextInput::make('sort_order')
                            ->label('Порядок сортировки')
                            ->numeric()
                            ->default(0),

                        Toggle::make('is_published')
                            ->label('Опубликован на главной')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Фото'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('partner_name')
                    ->label('Партнер')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Цена')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Статус')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Порядок')
                    ->sortable(),
            ])
            ->defaultSort('sort_order', 'asc')
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
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
            'index' => Pages\ListShowcaseProducts::route('/'),
            'create' => Pages\CreateShowcaseProduct::route('/create'),
            'edit' => Pages\EditShowcaseProduct::route('/{record}/edit'),
        ];
    }
}