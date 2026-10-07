<?php

namespace App\Filament\Resources\IncomingPackageResource\Pages;

use App\Filament\Resources\IncomingPackageResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditIncomingPackage extends EditRecord
{
    protected static string $resource = IncomingPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}