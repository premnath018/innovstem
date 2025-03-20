<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WebinarAttendanceResource\Pages;
use App\Models\WebinarAttendance;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\ExportAction;

class WebinarAttendanceResource extends Resource
{
    protected static ?string $model = WebinarAttendance::class;

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';

    protected static ?string $label = 'Webinar Attendances';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Select::make('student_id')
                ->relationship('student', 'name')
                ->required(),

            Forms\Components\Select::make('webinar_id')
                ->relationship('webinar', 'title')
                ->required(),

            Forms\Components\DatePicker::make('attended_at')->label('Attendance Date'),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('sno')
                    ->rowIndex()
                    ->label('S.No')
                    ->sortable(),

                TextColumn::make('student.name')
                    ->label('Student Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('webinar.title')
                    ->label('Webinar Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('student.mobile')
                    ->label('Mobile')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('student.standard')
                    ->label('Standard')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('attended_at')
                    ->label('Attended At')
                    ->dateTime('d M Y'),
            ])
            ->filters([
                SelectFilter::make('webinar_id')
                    ->label('Filter by Webinar')
                    ->relationship('webinar', 'title')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->modal(),
                Tables\Actions\EditAction::make()->modal(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),   
            ]);
    }


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWebinarAttendances::route('/'),
        ];
    }
}
