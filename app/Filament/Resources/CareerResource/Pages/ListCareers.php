<?php

namespace App\Filament\Resources\CareerResource\Pages;

use App\Filament\Exports\CareerExporter;
use App\Filament\Resources\CareerResource;
use Filament\Actions;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;

class ListCareers extends ListRecords
{
    protected static string $resource = CareerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()
            ->exporter(CareerExporter::class),
            Actions\CreateAction::make(),
        ];
    }
}
