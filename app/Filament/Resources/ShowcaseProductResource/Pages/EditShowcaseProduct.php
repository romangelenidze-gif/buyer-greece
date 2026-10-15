<?php

namespace App\Filament\Resources\ShowcaseProductResource\Pages;

use App\Filament\Resources\ShowcaseProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditShowcaseProduct extends EditRecord
{
    protected static string $resource = ShowcaseProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}