<?php

namespace App\Filament\Resources\ClassLevelResource\Pages;

use App\Filament\Resources\ClassLevelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClassLevel extends EditRecord
{
    protected static string $resource = ClassLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
