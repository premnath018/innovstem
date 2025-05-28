<?php

namespace App\Filament\Exports;

use App\Models\Career;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class CareerExporter extends Exporter
{
    protected static ?string $model = Career::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('title')
                ->label('Title'),
            ExportColumn::make('description')
                ->label('Description'),
            ExportColumn::make('location')
                ->label('Location'),
            ExportColumn::make('employment_type')
                ->label('Empployment Type'),
            ExportColumn::make('domain')
                ->label('Domain'),
            ExportColumn::make('experience')
                ->label('Experience'),
            ExportColumn::make('is_active')
                ->label('Active Status'),
            ExportColumn::make('registration_count')
                ->label('Application Count'),
            ExportColumn::make('created_at')
                ->label('Created At'),
            ExportColumn::make('updated_at')
                ->label('Updated At'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your career export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

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
        return "Careers -{$export->getKey()}.csv";
    }
}
