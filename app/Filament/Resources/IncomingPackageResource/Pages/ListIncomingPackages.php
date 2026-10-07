<?php

namespace App\Filament\Resources\IncomingPackageResource\Pages;

use App\Filament\Resources\IncomingPackageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIncomingPackages extends ListRecords
{
    protected static string $resource = IncomingPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}