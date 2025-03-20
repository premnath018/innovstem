<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuizAttemptResource\Pages;
use App\Models\QuizAttempt;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\ExportAction;

class QuizAttemptResource extends Resource
{
    protected static ?string $model = QuizAttempt::class;

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard';

    protected static ?string $label = 'Quiz Attempts';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Select::make('student_id')
                ->relationship('student', 'name')
                ->required(),

            Forms\Components\Select::make('quiz_id')
                ->relationship('quiz', 'title')
                ->required(),

            Forms\Components\TextInput::make('score')
                ->numeric()
                ->label('Score'),
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

                TextColumn::make('quiz.title')
                    ->label('Quiz Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('student.mobile')
                    ->label('Mobile')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('student.standard')
                    ->label('Standard')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('score')
                    ->label('Score')
                    ->sortable(),

                TextColumn::make('attempted_at')
                    ->label('Attempted At')
                    ->dateTime('d M Y'),
            ])
            ->filters([
                SelectFilter::make('quiz_id')
                    ->label('Filter by Quiz')
                    ->relationship('quiz', 'title')
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
            'index' => Pages\ListQuizAttempts::route('/'),
        ];
    }
}
