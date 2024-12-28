<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuizResource\Pages;
use App\Filament\Resources\QuizResource\RelationManagers;
use App\Models\Blog;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\Webinar;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Wizard;

class QuizResource extends Resource
{
    protected static ?string $model = Quiz::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Quiz Form')
    ->tabs([
        Forms\Components\Tabs\Tab::make('Basic Information')
            ->schema([
                Forms\Components\Select::make('quizable_type')
                    ->label('Select Type')
                    ->options([
                        Blog::class => 'Blog',
                        Webinar::class => 'Webinar',
                        Course::class => 'Course',
                    ])
                    ->live()
                    ->afterStateUpdated(fn (callable $set) => $set('quizable_id', null))
                    ->required(),

                Forms\Components\Select::make('quizable_id')
                    ->label('Select Item')
                    ->options(function (Forms\Get $get) {
                        $type = $get('quizable_type');
                        
                        if (!$type) {
                            return [];
                        }

                        return app($type)::query()
                            ->pluck('title', 'id')
                            ->toArray();
                    })
                    ->required()
                    ->disabled(fn (Forms\Get $get) => !$get('quizable_type')),

                Forms\Components\TextInput::make('title')
                    ->label('Quiz Title')
                    ->required(),
            ]),

        Forms\Components\Tabs\Tab::make('Questions & Options')
            ->schema([
                Forms\Components\Repeater::make('questions')
                    ->relationship('questions')
                    ->schema([
                        Forms\Components\Textarea::make('question_text')
                            ->label('Question')
                            ->required(),
                        Forms\Components\Repeater::make('options')
                            ->relationship('options')
                            ->schema([
                                Forms\Components\TextInput::make('option_text')
                                    ->label('Option')
                                    ->required(),
                                Forms\Components\Toggle::make('is_correct')
                                    ->label('Correct Answer')
                                    ->default(false),
                            ])
                            ->minItems(2)
                            ->label('Options'),
                    ])
                    ->label('Questions')
                    ->minItems(1),
            ]),
    ])->columnSpanFull()
 ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Quiz Title'),
                Tables\Columns\TextColumn::make('quizable_type')->label('Associated Type'),
                Tables\Columns\TextColumn::make('quizable.title')->label('Associated Item'),
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
            'index' => Pages\ListQuizzes::route('/'),
            'create' => Pages\CreateQuiz::route('/create'),
            'edit' => Pages\EditQuiz::route('/{record}/edit'),
        ];
    }
}
