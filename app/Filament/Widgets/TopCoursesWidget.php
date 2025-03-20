<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\CourseResource; // Adjust this namespace based on your CourseResource location
use App\Models\Course;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class TopCoursesWidget extends BaseWidget
{

    protected static ?string $pollingInterval = null;


    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 3; // Controls widget order on the dashboard

    public function table(Table $table): Table
    {
        return $table
        ->query(
            CourseResource::getEloquentQuery()
                ->orderByDesc('enrolment_count') // Directly sort by enroll_count column
                ->limit(5) // Fetch only top 5 courses
        )
            ->defaultPaginationPageOption(5)
            ->paginated(false)
            ->defaultSort('enrolment_count', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Published Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Course Title')
                    ->sortable(),
                Tables\Columns\TextColumn::make('enrolment_count')
                    ->label('Enrollments')
                    ->sortable()
                    ->default(0),
            ])
            ->actions([]);
    }
}
