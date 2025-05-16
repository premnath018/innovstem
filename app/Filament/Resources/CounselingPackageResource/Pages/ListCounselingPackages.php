<?php

namespace App\Filament\Resources\CounselingPackageResource\Pages;

use App\Filament\Resources\CounselingPackageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCounselingPackages extends ListRecords
{
    protected static string $resource = CounselingPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
