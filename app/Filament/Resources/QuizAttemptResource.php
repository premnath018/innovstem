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
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Exports\QuizAttemptExporter;

class QuizAttemptResource extends Resource
{
    protected static ?string $model = QuizAttempt::class;

    protected static ?string $navigationGroup = 'Student Management';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard';

    protected static ?string $label = 'Quiz Attempts';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Select::make('student_id')
                ->label('Student')
                ->relationship('student', 'name')
                ->required()
                ->native(false)
                ->searchable(),
            Forms\Components\Select::make('quiz_id')
                ->label('Quiz')
                ->relationship('quiz', 'title')
                ->required()
                ->native(false)
                ->searchable(),
            Forms\Components\TextInput::make('score')
                ->label('Score (%)')
                ->numeric()
                ->required()
                ->minValue(0)
                ->maxValue(100),
            Forms\Components\TextInput::make('correct_answers')
                ->label('Correct Answers')
                ->numeric()
                ->required()
                ->minValue(0),
            Forms\Components\TextInput::make('incorrect_answers')
                ->label('Incorrect Answers')
                ->numeric()
                ->required()
                ->minValue(0),
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
                    ->searchable(),
                TextColumn::make('score')
                    ->label('Score (%)')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state . '%'),
                TextColumn::make('correct_answers')
                    ->label('Correct')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('incorrect_answers')
                    ->label('Incorrect')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('attempted_at')
                    ->label('Attempted At')
                    ->dateTime('d M Y'),
            ])
            ->filters([
                SelectFilter::make('quiz_id')
                    ->label('Quiz')
                    ->relationship('quiz', 'title')
                    ->searchable()
                    ->native(false),
                Tables\Filters\Filter::make('name_search')
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->label('Search Student Name')
                            ->placeholder('Enter student name'),
                    ])
                    ->query(function (Builder $query, $data): Builder {
                        return $query->when(
                            $data['name'],
                            fn (Builder $query, $value) => $query->whereHas('student', fn ($q) => $q->where('name', 'like', '%' . $value . '%'))
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['name'] ?? null) {
                            return 'Searching for student: ' . $data['name'];
                        }
                        return null;
                    }),
                    Tables\Filters\Filter::make('attempted_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Attempted From')
                            ->native(false),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Attempted Until')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date) => $query->whereDate('attempted_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date) => $query->whereDate('attempted_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators[] = 'Created From: ' . \Carbon\Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators[] = 'Created Until: ' . \Carbon\Carbon::parse($data['created_until'])->toFormattedDateString();
                        }
                        return $indicators;
                    })
                    ->label('Attempted Date Range'),
                Tables\Filters\SelectFilter::make('min_correct_answers')
                    ->label('Minimum Correct Answers')
                    ->options([
                        '0' => 'Any',
                        '1' => '1+',
                        '5' => '5+',
                        '10' => '10+',
                    ])
                    ->native(false)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] && $data['value'] !== '0',
                            fn (Builder $query, $value) => $query->where('correct_answers', '>=', $data['value']),
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['value'] && $data['value'] !== '0') {
                            return 'Minimum Correct Answers: ' . $data['value'];
                        }
                        return null;
                    }),
            ])
            ->persistFiltersInSession()
            ->actions([
                Tables\Actions\ViewAction::make()->modal(),
                Tables\Actions\EditAction::make()->modal(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(QuizAttemptExporter::class)
                    ->label('Export Attempts')
                    ->color('primary')
                    ->icon('heroicon-o-arrow-down-tray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Grid::make(2)
                    ->schema([
                        TextEntry::make('student.name')
                            ->label('Student Name')
                            ->weight('bold'),
                        TextEntry::make('quiz.title')
                            ->label('Quiz Name'),
                        TextEntry::make('score')
                            ->label('Score')
                            ->formatStateUsing(fn ($state) => $state . '%'),
                        TextEntry::make('correct_answers')
                            ->label('Correct Answers'),
                        TextEntry::make('incorrect_answers')
                            ->label('Incorrect Answers'),
                        TextEntry::make('student.mobile')
                            ->label('Mobile'),
                        TextEntry::make('student.standard')
                            ->label('Standard')
                            ->badge(),
                        TextEntry::make('attempted_at')
                            ->label('Attempted At')
                            ->dateTime('d M Y'),
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