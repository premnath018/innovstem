<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Models\Course;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationGroup = 'Content Management System';

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?int $navigationSort = 4;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('General Information')
                    ->schema([
                        TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                // Auto-generate slug and meta title if toggles are enabled
                                if ($get('sync_slug')) {
                                    $set('course_slug', Str::slug($state));
                                }
                                if ($get('sync_meta_title')) {
                                    $set('course_meta_title', $state);
                                }
                            }),
                        Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name')
                            ->native(false)
                            ->required(),
                        Textarea::make('content_short_description')
                            ->label('Short Description')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                // Sync meta description if toggle is enabled
                                if ($get('sync_meta_description')) {
                                    $set('course_meta_description', $state);
                                }
                            }),
                        Select::make('class_level_id')
                            ->label('Class Level')
                            ->relationship('classLevel', 'name')
                            ->native(false)
                            ->required(),
                        Textarea::make('content_long_description')
                            ->columnSpanFull()
                            ->label('Long Description'),
                    ])->columns(2),
                Forms\Components\Fieldset::make('Options')
                    ->schema([
                        Toggle::make('sync_meta_title')
                            ->label('Generate Meta Title')
                            ->default(true)
                            ->reactive()
                            ->inline(),
                        Toggle::make('sync_slug')
                            ->label('Sync Slug')
                            ->default(true)
                            ->reactive()
                            ->inline(),
                        Toggle::make('sync_meta_description')
                            ->label('Sync Meta Description')
                            ->default(true)
                            ->reactive()
                            ->inline(),
                    ])
                    ->columns(3),
                Forms\Components\Section::make('Course Details and Media')
                    ->schema([
                        RichEditor::make('course_content')
                            ->label('Course Content')
                            ->required()
                            ->columnSpanFull(),
                        FileUpload::make('course_banner')
                            ->label('Banner Image')
                            ->directory('courses/banners')
                            ->visibility('public')
                            ->image()
                            ->required()
                            ->maxSize(2048),
                        FileUpload::make('course_thumbnail')
                            ->label('Thumbnail Image')
                            ->directory('courses/thumbnails')
                            ->visibility('public')
                            ->image()
                            ->required()
                            ->maxSize(1024),
                        Textarea::make('learning_materials')
                            ->columnSpanFull()
                            ->label('Learning Materials'),
                    ])->columns(2),
                Forms\Components\Section::make('Meta Information')
                    ->schema([
                        TextInput::make('course_slug')
                            ->label('Slug')
                            ->required()
                            ->disabled(fn (Get $get) => $get('sync_slug'))
                            ->maxLength(255)
                            ->dehydrated()
                            ->unique(Course::class, 'course_slug', ignoreRecord: true),
                        TextInput::make('course_meta_title')
                            ->label('Meta Title')
                            ->required()
                            ->disabled(fn (Get $get) => $get('sync_meta_title'))
                            ->maxLength(255)
                            ->dehydrated(),
                        Textarea::make('course_meta_description')
                            ->label('Meta Description')
                            ->required()
                            ->disabled(fn (Get $get) => $get('sync_meta_description'))
                            ->columnSpanFull()
                            ->dehydrated(),
                        Textarea::make('course_meta_keyword')
                            ->label('Meta Keywords')
                            ->placeholder('Separated by commas')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('created_by')
                            ->label('Author')
                            ->required(),
                        Select::make('active')
                            ->label('Active')
                            ->options([0 => 'Inactive', 1 => 'Active'])
                            ->required()
                            ->native(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->rowIndex()
                    ->label('Index'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => [
                        'primary', 'secondary', 'success', 'danger', 'warning', 'info',
                    ][crc32($state) % 6] ?? 'primary')
                    ->searchable(),
                Tables\Columns\TextColumn::make('classLevel.name')
                    ->label('Class')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => [
                        'primary', 'secondary', 'success', 'danger', 'warning', 'info',
                    ][crc32($state) % 6] ?? 'primary')
                    ->searchable(),
                Tables\Columns\TextColumn::make('view_count')
                    ->label('Views')
                    ->sortable()
                    ->numeric(),
                Tables\Columns\TextColumn::make('enrolment_count')
                    ->label('Enrolments')
                    ->sortable()
                    ->numeric(),
                Tables\Columns\IconColumn::make('active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->native(false)
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('class_level_id')
                    ->label('Class Level')
                    ->relationship('classLevel', 'name')
                    ->native(false)
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('active')
                    ->label('Status')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ])
                    ->native(false),
                Tables\Filters\Filter::make('title_search')
                    ->form([
                        Forms\Components\TextInput::make('title')
                            ->label('Search Title')
                            ->placeholder('Enter course title'),
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
                Tables\Filters\SelectFilter::make('min_enrolments')
                    ->label('Minimum Enrolments')
                    ->options([
                        '0' => 'Any',
                        '10' => '10+',
                        '50' => '50+',
                    ])
                    ->native(false)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] && $data['value'] !== '0',
                            fn (Builder $query, $value): Builder => $query->where('enrolment_count', '>=', $data['value'])
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['value'] && $data['value'] !== '0') {
                            return 'Minimum Enrolments: ' . $data['value'];
                        }
                        return null;
                    }),
            ])
            ->persistFiltersInSession()
            ->searchOnBlur()
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
                Section::make('General Information')
                    ->schema([
                        TextEntry::make('title')
                            ->label('Title'),
                        TextEntry::make('category.name')
                            ->label('Category'),
                        TextEntry::make('classLevel.name')
                            ->label('Class Level'),
                        TextEntry::make('content_short_description')
                            ->label('Short Description'),
                        TextEntry::make('content_long_description')
                            ->label('Long Description')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Course Content and Media')
                    ->schema([
                        ImageEntry::make('course_banner')
                            ->label('Banner Image')
                            ->columnSpanFull()
                            ->extraAttributes(['class' => 'w-full h-auto']),
                        ImageEntry::make('course_thumbnail')
                            ->label('Thumbnail Image')
                            ->columnSpanFull()
                            ->extraAttributes(['class' => 'w-full h-auto']),
                        TextEntry::make('course_content')
                            ->label('Content')
                            ->html()
                            ->columnSpanFull(),
                        TextEntry::make('learning_materials')
                            ->label('Learning Materials')
                            ->html()
                            ->columnSpanFull()
                            ->separator('@$')
                            ->bulleted(),
                    ])
                    ->columns(2),
                Section::make('Meta Information')
                    ->schema([
                        TextEntry::make('course_meta_title')
                            ->label('Meta Title'),
                        TextEntry::make('course_slug')
                            ->label('Slug'),
                        TextEntry::make('course_meta_description')
                            ->label('Meta Description')
                            ->columnSpanFull(),
                        TextEntry::make('course_meta_keyword')
                            ->label('Meta Keywords')
                            ->columnSpanFull(),
                        TextEntry::make('created_by')
                            ->label('Author'),
                        TextEntry::make('view_count')
                            ->label('View Count'),
                        TextEntry::make('enrolment_count')
                            ->label('Enrolment Count'),
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getRelations(): array
    {
        return [
            // Define relations here if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
            'view' => Pages\ViewCourse::route('/{record}'),
        ];
    }
}