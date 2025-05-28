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
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Illuminate\Database\Eloquent\Builder;

class QuizResource extends Resource
{
    protected static ?string $model = Quiz::class;

    protected static ?int $navigationSort = 8;

    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';

    protected static ?string $navigationGroup = 'Content Management System';

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
                                    ->native(false)
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
                                    ->native(false)
                                    ->required()
                                    ->disabled(fn (Forms\Get $get) => !$get('quizable_type'))
                                    ->searchable(),
                                Forms\Components\TextInput::make('title')
                                    ->label('Quiz Title')
                                    ->required(),
                                Forms\Components\Toggle::make('retry')
                                    ->label('Allow Retry')
                                    ->default(true),
                                Forms\Components\Toggle::make('mix')
                                    ->label('Mix Up')
                                    ->default(true),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true),
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
                    ])
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Quiz Title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quizable_type')
                    ->label('Associated Type')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        Blog::class => 'Blog',
                        Webinar::class => 'Webinar',
                        Course::class => 'Course',
                        default => 'Unknown'
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('quizable.title')
                    ->label('Associated Item')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('retry')
                    ->label('Retry Allowed')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('title_search')
                    ->form([
                        Forms\Components\TextInput::make('title')
                            ->label('Search Quiz Title')
                            ->placeholder('Enter quiz title'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['title'],
                            fn (Builder $query, $value): Builder => $query->where('title', 'like', '%' . $value . '%')
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['title'] ?? null) {
                            return 'Searching for title: ' . $data['title'];
                        }
                        return null;
                    }),
                Tables\Filters\SelectFilter::make('quizable_type')
                    ->label('Associated Type')
                    ->options([
                        Blog::class => 'Blog',
                        Webinar::class => 'Webinar',
                        Course::class => 'Course',
                    ])
                    ->native(false)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $value): Builder => $query->where('quizable_type', $value)
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['value'] ?? null) {
                            return 'Associated Type: ' . match ($data['value']) {
                                Blog::class => 'Blog',
                                Webinar::class => 'Webinar',
                                Course::class => 'Course',
                                default => 'Unknown'
                            };
                        }
                        return null;
                    }),
                Tables\Filters\SelectFilter::make('quizable_id')
                    ->label('Associated Item')
                    ->options(function () {
                        $blogs = Blog::pluck('title', 'id')->mapWithKeys(fn ($title, $id) => ['Blog_' . $id => 'Blog: ' . $title]);
                        $webinars = Webinar::pluck('title', 'id')->mapWithKeys(fn ($title, $id) => ['Webinar_' . $id => 'Webinar: ' . $title]);
                        $courses = Course::pluck('title', 'id')->mapWithKeys(fn ($title, $id) => ['Course_' . $id => 'Course: ' . $title]);
                        return $blogs->merge($webinars)->merge($courses)->toArray();
                    })
                    ->native(false)
                    ->searchable()
                    ->query(function (Builder $query, array $data): Builder {
                        if ($data['value']) {
                            [$type, $id] = explode('_', $data['value']);
                            $quizableType = match ($type) {
                                'Blog' => Blog::class,
                                'Webinar' => Webinar::class,
                                'Course' => Course::class,
                                default => null
                            };
                            if ($quizableType) {
                                return $query->where('quizable_type', $quizableType)->where('quizable_id', $id);
                            }
                        }
                        return $query;
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['value'] ?? null) {
                            [$type, $id] = explode('_', $data['value']);
                            $model = match ($type) {
                                'Blog' => Blog::find($id),
                                'Webinar' => Webinar::find($id),
                                'Course' => Course::find($id),
                                default => null
                            };
                            return $model ? "Associated Item: {$type}: {$model->title}" : null;
                        }
                        return null;
                    }),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Created From')
                            ->native(false),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Created Until')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date)
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date)
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
                    ->label('Created Date Range'),
                Tables\Filters\SelectFilter::make('retry')
                    ->label('Retry Allowed')
                    ->options([
                        '1' => 'Yes',
                        '0' => 'No',
                    ])
                    ->native(false),
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ])
                    ->native(false),
            ])
            ->persistFiltersInSession()
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
                Section::make('Quiz Information')
                    ->schema([
                        TextEntry::make('title')
                            ->label('Quiz Title')
                            ->weight('bold'),
    
                        TextEntry::make('quizable_type')
                            ->label('Associated Type')
                            ->formatStateUsing(function ($state) {
                                return match ($state) {
                                    Blog::class => 'Blog',
                                    Webinar::class => 'Webinar',
                                    Course::class => 'Course',
                                    default => 'Unknown',
                                };
                            }),
    
                        TextEntry::make('quizable.title')
                            ->label('Associated Item'),
    
                        TextEntry::make('retry')
                            ->label('Retry Allowed')
                            ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No')
                            ->badge()
                            ->color(fn ($state) => $state ? 'success' : 'danger'),
    
                        TextEntry::make('is_active')
                            ->label('Active Status')
                            ->formatStateUsing(fn ($state) => $state ? 'Active' : 'Inactive')
                            ->badge()
                            ->color(fn ($state) => $state ? 'success' : 'danger'),
    
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                    ])
                    ->columns(2),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            // Relations can be added here if needed
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
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
