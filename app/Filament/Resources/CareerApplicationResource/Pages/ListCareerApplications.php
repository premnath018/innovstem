<?php

namespace App\Filament\Resources\CareerApplicationResource\Pages;

use App\Filament\Resources\CareerApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCareerApplications extends ListRecords
{
    protected static string $resource = CareerApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
