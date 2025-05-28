<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogResource\Pages;
use App\Models\Blog;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Components\RichEditor;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;

class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;

    protected static ?string $navigationGroup = 'Content Management System';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationIcon = 'heroicon-o-pencil';

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
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                // Only update slug if it matches the old title's slug
                                if ($get('sync_slug')) {
                                    $set('blog_slug', Str::slug($state));
                                }
                                // Always set meta title to match title
                                if ($get('sync_meta_title')) {
                                    $set('blog_meta_title', $state);
                                }
                            }),
                        Forms\Components\Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name')
                            ->required()
                            ->native(false),
                        Textarea::make('blog_description')
                            ->label('Short Description')
                            ->required()
                            ->live(onBlur: true)
                            ->columnSpanFull()
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                // Sync meta description if toggle is enabled
                                if ($get('sync_meta_description')) {
                                    $set('blog_meta_description', $state);
                                }
                            }),
                    ])->columns(2),

                    Forms\Components\Fieldset::make('Options')
                    ->schema([
                        Toggle::make('sync_meta_title')
                            ->label('Generate Meta Title')
                            ->statePath('sync_meta_title')
                            ->default(true)
                            ->reactive()
                            ->inline(),
                        Toggle::make('sync_slug')
                            ->label('Sync Slug')
                            ->statePath('sync_slug')
                            ->default(true)
                            ->reactive()
                            ->inline(),
                        Toggle::make('sync_meta_description')
                            ->label('Sync Meta Description')
                            ->statePath('sync_meta_description')
                            ->default(true)
                            ->reactive()
                            ->inline(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Blog Content and Media')
                    ->schema([
                        RichEditor::make('blog_content')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('blog_banner')
                            ->label('Banner Image')
                            ->directory('blogs/banners')
                            ->visibility('public')
                            ->image()
                            ->required()
                            ->maxSize(2048), // 2MB max size
                        Forms\Components\FileUpload::make('blog_thumbnail')
                            ->label('Thumbnail Image')
                            ->directory('blogs/thumbnails')
                            ->visibility('public')
                            ->image()
                            ->required()
                            ->maxSize(1024), // 1MB max size
                    ])->columns(2),

                    Forms\Components\Section::make('Meta Information')
                    ->schema([
                        TextInput::make('blog_meta_title')
                            ->label('Meta Title')
                            ->required()
                            ->disabled(fn (Get $get) => $get('sync_meta_title'))
                            ->dehydrated()
                            ->maxLength(255),
                        TextInput::make('blog_slug')
                            ->label('Slug')
                            ->required()
                            ->disabled(fn (Get $get) => $get('sync_slug'))
                            ->maxLength(255)
                            ->dehydrated()
                            ->unique(Blog::class, 'blog_slug', ignoreRecord: true),
                        Textarea::make('blog_meta_description')
                            ->label('Meta Description')
                            ->disabled(fn (Get $get) => $get('sync_meta_description'))
                            ->required()
                            ->columnSpanFull()
                            ->dehydrated(),
                        Textarea::make('blog_meta_keyword')
                            ->label('Meta Keywords')
                            ->placeholder('Separated By Commas')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('created_by')
                            ->label('Author'),
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
                    ->badge()
                    ->color(fn (string $state): string => [
                        'primary', 'secondary', 'success', 'danger', 'warning', 'info',
                    ][crc32($state) % 6] ?? 'primary')
                    ->searchable(),
                Tables\Columns\TextColumn::make('view_count')
                    ->label('Views')
                    ->sortable()
                    ->numeric(),
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
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('active')
                    ->label('Status')
                    ->native(false)
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),
                Tables\Filters\Filter::make('title_search')
                    ->form([
                        Forms\Components\TextInput::make('title')
                            ->label('Search Title')
                            ->placeholder('Enter blog title'),
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
            ])
            ->persistFiltersInSession()
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
                        TextEntry::make('blog_description')
                            ->label('Short Description')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Blog Content and Media')
                    ->schema([
                        ImageEntry::make('blog_banner')
                            ->label('Banner Image')
                            ->columnSpanFull()
                            ->extraAttributes(['class' => 'w-full h-auto']),
                        ImageEntry::make('blog_thumbnail')
                            ->label('Thumbnail Image')
                            ->columnSpanFull()
                            ->extraAttributes(['class' => 'w-full h-auto']),
                        TextEntry::make('blog_content')
                            ->label('Content')
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Meta Information')
                    ->schema([
                        TextEntry::make('blog_meta_title')
                            ->label('Meta Title'),
                        TextEntry::make('blog_slug')
                            ->label('Slug'),
                        TextEntry::make('blog_meta_description')
                            ->label('Meta Description')
                            ->columnSpanFull(),
                        TextEntry::make('blog_meta_keyword')
                            ->label('Meta Keywords')
                            ->columnSpanFull(),
                        TextEntry::make('created_by')
                            ->label('Author'),
                        TextEntry::make('view_count')
                            ->label('View Count'),
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

    public static function getRelations(): array
    {
        return [
            // Define relations here if needed
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlog::route('/create'),
            'edit' => Pages\EditBlog::route('/{record}/edit'),
            'view' => Pages\ViewBlog::route('/{record}'),
        ];
    }
}