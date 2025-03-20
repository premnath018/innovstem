<?php

namespace App\Filament\Imports;

use App\Models\CourseEnrollment;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourseEnrollmentImporter extends Importer
{
    protected static ?string $model = CourseEnrollment::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('student_id')
                ->requiredMapping()
                ->rules(['required', 'integer']),
            ImportColumn::make('course_id')
                ->requiredMapping()
                ->rules(['required', 'integer']),
        ];
    }

    public function resolveRecord(): ?CourseEnrollment
    {
        return DB::transaction(function () {
            // Ensure both IDs are present and valid
            if (empty($this->data['student_id']) || empty($this->data['course_id'])) {
                Log::error("Invalid data: student_id or course_id missing", $this->data);
                return null; // Skip this row if either ID is missing
            }

            // Create or retrieve the enrollment using the provided IDs
            return CourseEnrollment::firstOrCreate([
                'student_id' => $this->data['student_id'],
                'course_id' => $this->data['course_id'],
            ]);
        });
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Course enrollment import completed. ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}