<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IncomingPackageResource\Pages\CreateIncomingPackage;
use App\Filament\Resources\IncomingPackageResource\Pages\EditIncomingPackage;
use App\Filament\Resources\IncomingPackageResource\Pages\ListIncomingPackages;
use App\Filament\Resources\IncomingPackageResource\Schemas\IncomingPackageForm;
use App\Filament\Resources\IncomingPackageResource\Tables\IncomingPackagesTable;
use App\Models\IncomingPackage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IncomingPackageResource extends Resource
{
    protected static ?string $model = IncomingPackage::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-cube';

    public static function form(Schema $schema): Schema
    {
        return IncomingPackageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IncomingPackagesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIncomingPackages::route('/'),
            'create' => CreateIncomingPackage::route('/create'),
            'edit' => EditIncomingPackage::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
