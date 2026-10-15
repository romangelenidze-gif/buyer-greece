<?php

namespace App\Filament\Resources\ShowcaseProductResource\Pages;

use App\Filament\Resources\ShowcaseProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShowcaseProducts extends ListRecords
{
    protected static string $resource = ShowcaseProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}