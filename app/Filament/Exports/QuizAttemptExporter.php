<?php

namespace App\Filament\Exports;

use App\Models\QuizAttempt;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Exports\Enums\ExportFormat;

class QuizAttemptExporter extends Exporter
{
    protected static ?string $model = QuizAttempt::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('Id'),
            ExportColumn::make('student.name')
                ->label('Student Name'),
            ExportColumn::make('student.mobile')
                ->label('Mobile'),
            ExportColumn::make('student.standard')
                ->label('Standard'),
            ExportColumn::make('quiz.title')
                ->label('Quiz Name'),
            ExportColumn::make('score')
                ->label('Score (%)')
                ->formatStateUsing(fn ($state) => $state . '%'),
            ExportColumn::make('correct_answers')
                ->label('Correct Answers'),
            ExportColumn::make('incorrect_answers')
                ->label('Incorrect Answers'),
            ExportColumn::make('attempted_at')
                ->label('Attempted At')
                ->formatStateUsing(fn ($state) => $state->format('d M Y')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your quiz attempt export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

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
        return "Quiz Attempts -{$export->getKey()}.csv";
    }
}