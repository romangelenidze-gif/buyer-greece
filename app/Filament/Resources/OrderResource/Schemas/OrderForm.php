<?php

namespace App\Filament\Resources\OrderResource\Schemas;

use App\Enums\OrderType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('public_order_number')
                    ->required(),
                Select::make('customer_id')
                    ->relationship('customer', 'id')
                    ->required(),
                Select::make('type')
                    ->options(OrderType::class)
                    ->required(),
                TextInput::make('status')
                    ->required(),
                Select::make('active_quote_id')
                    ->relationship('activeQuote', 'id'),
                Textarea::make('internal_note')
                    ->columnSpanFull(),
                DateTimePicker::make('submitted_at')
                    ->required(),
                DateTimePicker::make('completed_at'),
            ]);
    }
}
