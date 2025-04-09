<?php

namespace App\Filament\Resources\CareerApplicationResource\Pages;

use App\Filament\Resources\CareerApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCareerApplication extends EditRecord
{
    protected static string $resource = CareerApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
