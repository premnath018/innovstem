<?php

namespace App\Filament\Resources\ClassLevelResource\Pages;

use App\Filament\Resources\ClassLevelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClassLevels extends ListRecords
{
    protected static string $resource = ClassLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->createAnother(false),
        ];
    }
}
