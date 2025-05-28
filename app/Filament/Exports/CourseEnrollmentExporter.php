<?php

namespace App\Filament\Exports;

use App\Models\CourseEnrollment;
use App\Models\Student;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Exports\Enums\ExportFormat;

class CourseEnrollmentExporter extends Exporter
{
    protected static ?string $model = CourseEnrollment::class;




    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),

            ExportColumn::make('student_id')
            ->label('Student ID'),

            ExportColumn::make('student_name')
            ->label('Student Name')
            ->state(function (CourseEnrollment $record) {
                return Student::where('id', $record->student_id)->value('name') ?? '-';
            }),
            
            ExportColumn::make('student_mobile')
                ->label('Mobile No')
                ->state(function (CourseEnrollment $record) {
                    return Student::where('id', $record->student_id)->value('mobile') ?? '-';
                }),
            
            ExportColumn::make('course_id')
            ->label('Student ID'),    

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

    public function getFormats(): array
    {
        return [
            ExportFormat::Csv,
        ];
    }

    public function getFileName(Export $export): string
    {
        return "Course Enrollments -{$export->getKey()}.csv";
    }
}
