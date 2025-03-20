<?php

namespace App\Filament\Exports;

use App\Models\CourseEnrollment;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class CourseEnrollmentExporter extends Exporter
{
    protected static ?string $model = CourseEnrollment::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('student.name')->name('Student Name'),
            ExportColumn::make('student.mobile')->name('Mobile No'),
            ExportColumn::make('course.category.name'),
            ExportColumn::make('course.title'),
            ExportColumn::make('enrolled_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your course enrollment export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
