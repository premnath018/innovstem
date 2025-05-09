<?php

namespace App\Filament\Exports;

use App\Enums\TaskStatus;
use App\Models\Task;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TaskExporter extends Exporter
{
    protected static ?string $model = Task::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('title')->label('Task Title'),
            ExportColumn::make('description')->label('Description'),
            ExportColumn::make('status')
                ->label('Status')
                ->formatStateUsing(fn ($state) => str_replace('_', ' ', ucwords($state))),
            ExportColumn::make('priority')
                ->label('Priority')
                ->formatStateUsing(fn ($state) => ucfirst($state)),
            ExportColumn::make('assignedTo.name')
                ->label('Assigned To')
                ->default('Not assigned'),
            ExportColumn::make('createdBy.name')
                ->label('Created By')
                ->default('Unknown'),
            ExportColumn::make('deadline_date')
                ->label('Deadline')
                ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->toFormattedDateString() : 'No deadline'),
            ExportColumn::make('remarks')
                ->label('Remarks')
                ->default('No remarks'),
            ExportColumn::make('proof_url')
                ->label('Proof Image URL')
                ->formatStateUsing(fn ($state) => $state ? \Illuminate\Support\Facades\Storage::url($state) : 'No proof'),
            ExportColumn::make('created_at')
                ->label('Created At')
                ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->toDateTimeString()),
            ExportColumn::make('updated_at')
                ->label('Updated At')
                ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->toDateTimeString()),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your task export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}