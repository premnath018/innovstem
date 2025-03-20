<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\BlogResource; // Adjust this namespace based on your BlogResource location
use App\Models\Blog;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopBlogsWidget extends BaseWidget
{

    protected static ?string $pollingInterval = null;


    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2; // Controls widget order on the dashboard

    public function table(Table $table): Table
    {
        return $table
        ->query(
            BlogResource::getEloquentQuery()
                ->orderByDesc('view_count') // Order by views count in descending order
                ->limit(10) // Get only the top 10 blogs
        )
        ->defaultPaginationPageOption(10) 
        ->paginated(false)
        ->defaultSort('view_count', 'desc') // Sort by latest first
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Published Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->sortable(),
                Tables\Columns\TextColumn::make('view_count') // Assuming a views column
                    ->label('View Count')
                    ->sortable()
                    ->default(0),
            ])
            ->actions([
            ]);
    }
}