<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseEnrollmentResource\Pages;
use App\Models\CourseEnrollment;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\ExportAction;

class CourseEnrollmentResource extends Resource
{
    protected static ?string $model = CourseEnrollment::class;

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $label = 'Course Enrollment';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Select::make('student_id')
                ->relationship('student', 'name')
                ->required(),

            Forms\Components\Select::make('course_id')
                ->relationship('course', 'title')
                ->required(),

            Forms\Components\DatePicker::make('enrolled_at')->label('Enrollment Date'),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('sno')
                    ->label('S.No')
                    ->rowIndex()
                    ->sortable(),

                TextColumn::make('student.name')
                    ->label('Student Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('course.title')
                    ->label('Course Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('student.mobile')
                    ->label('Mobile')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('student.standard')
                    ->label('Standard')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('student.ambition')
                    ->label('Ambition')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('student.district')
                    ->label('District')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('enrolled_at')
                    ->label('Enrolled At')
                    ->dateTime('d M Y'),
            ])
            ->filters([
                SelectFilter::make('course_id')
                    ->label('Filter by Course')
                    ->relationship('course', 'title')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->modal(),
                Tables\Actions\EditAction::make()->modal(),
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
            'index' => Pages\ListCourseEnrollments::route('/'),
        ];
    }
}
