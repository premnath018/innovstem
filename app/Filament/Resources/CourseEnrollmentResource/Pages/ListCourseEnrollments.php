<?php

namespace App\Filament\Resources\CourseEnrollmentResource\Pages;

use App\Filament\Exports\CourseEnrollmentExporter;
use App\Filament\Imports\CourseEnrollmentImporter;
use App\Filament\Resources\CourseEnrollmentResource;
use Filament\Actions;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\ImportAction;

class ListCourseEnrollments extends ListRecords
{
    protected static string $resource = CourseEnrollmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()
            ->exporter(CourseEnrollmentExporter::class),
            ImportAction::make()
            ->importer(CourseEnrollmentImporter::class),
            Actions\CreateAction::make(),
        ];
    }
}
