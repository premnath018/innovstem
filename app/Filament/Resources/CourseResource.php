<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Filament\Resources\CourseResource\RelationManagers;
use App\Models\Course;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationGroup = 'Content Management System';

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('course_slug')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('course_title')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('content_short_description')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('content_long_description')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('course_content')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('learning_materials')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('course_banner')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('course_thumbnail')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('category_id')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('created_by')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('course_meta_title')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('course_meta_keyword')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('course_meta_description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('class_level')
                    ->required(),
                Forms\Components\TextInput::make('view_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('enrolment_count')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('class_level'),
                Tables\Columns\TextColumn::make('view_count')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('enrolment_count')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
