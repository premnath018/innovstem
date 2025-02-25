<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\WebinarResource; // Adjust if needed
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopWebinarsWidget extends BaseWidget
{
    use HasWidgetShield;

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 4; // Controls widget order on the dashboard

    public function table(Table $table): Table
    {
        return $table
            ->query(
                WebinarResource::getEloquentQuery()
                    ->orderByDesc('attendance_count') // Directly sort by the attendance_count column
                    ->limit(10) // Fetch only top 10 webinars
            )
            ->defaultPaginationPageOption(10)
            ->paginated(false)
            ->defaultSort('attendance_count', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Published Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Webinar Title')
                    ->sortable(),
                Tables\Columns\TextColumn::make('attendance_count') // Using the existing column
                    ->label('Attendees')
                    ->sortable()
                    ->default(0),
            ])
            ->actions([]);
    }
}
