<?php

namespace App\Filament\Resources\IncomingPackageResource\Schemas;

use App\Enums\PackageSourceType;
use App\Enums\PackageStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class IncomingPackageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('public_package_number')
                    ->required(),
                Select::make('customer_id')
                    ->relationship('customer', 'id'),
                Select::make('order_id')
                    ->relationship('order', 'id'),
                Select::make('shipment_id')
                    ->relationship('shipment', 'id'),
                Select::make('source_type')
                    ->options(PackageSourceType::class)
                    ->required(),
                TextInput::make('store_name'),
                TextInput::make('store_tracking_number'),
                Textarea::make('description')
                    ->columnSpanFull(),
                DatePicker::make('expected_date'),
                DateTimePicker::make('received_at'),
                TextInput::make('weight_kg')
                    ->numeric(),
                TextInput::make('dimensions'),
                Select::make('status')
                    ->options(PackageStatus::class)
                    ->required(),
                Textarea::make('photos_path')
                    ->columnSpanFull(),
                Textarea::make('internal_note')
                    ->columnSpanFull(),
            ]);
    }
}
