<?php

namespace App\Filament\Exports;

use App\Models\CareerApplication;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class CareerApplicationExporter extends Exporter
{
    protected static ?string $model = CareerApplication::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
            ->label('ID'),
            ExportColumn::make('career.name')
            ->label('Job Applied'),
            ExportColumn::make('applicant_name')
            ->label('Applicant Name'),
            ExportColumn::make('email')
            ->label('Applicant Email'),
            ExportColumn::make('phone')
            ->label('Applicant Phone'),
            ExportColumn::make('cover_letter')
            ->label('Cover Letter'),
            ExportColumn::make('resume_path')
            ->label('Resume Link')
            ->formatStateUsing(fn ($state) => $state ? \Illuminate\Support\Facades\Storage::url($state) : 'No proof'),
            ExportColumn::make('status')
            ->label('Status'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your career application export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
