<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResourceResource\Pages;
use App\Models\Resource as ResourceModel;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

class ResourceResource extends Resource
{
    protected static ?string $model = ResourceModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationGroup = 'Content Management System';

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
                            ->afterStateUpdated(function ($state, $set, $get) {
                                if ($get('sync_slug')) {
                                    $set('resource_slug', Str::slug($state));
                                }
                                if ($get('sync_meta_title')) {
                                    $set('resource_meta_title', $state);
                                }
                            }),
                        Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name')
                            ->native(false)
                            ->required(),
                        Textarea::make('resource_description')
                            ->label('Description')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set, $get) {
                                if ($get('sync_meta_description')) {
                                    $set('resource_meta_description', $state);
                                }
                            }),
                    ])->columns(2),
                Forms\Components\Fieldset::make('Options')
                    ->schema([
                        Toggle::make('sync_meta_title')
                            ->label('Generate Meta Title')
                            ->default(true),
                        Toggle::make('sync_slug')
                            ->label('Sync Slug')
                            ->default(true),
                        Toggle::make('sync_meta_description')
                            ->label('Sync Meta Description')
                            ->default(true),
                    ])->columns(3),
                Forms\Components\Section::make('Media & Content')
                    ->schema([
                        RichEditor::make('resource_content')
                            ->label('Content')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('resource_url')
                            ->label('URL')
                            ->required(),
                        FileUpload::make('resource_banner')
                            ->label('Banner Image')
                            ->directory('resources/banners')
                            ->visibility('public')
                            ->image()
                            ->required()
                            ->maxSize(2048),
                        FileUpload::make('resource_thumbnail')
                            ->label('Thumbnail Image')
                            ->directory('resources/thumbnails')
                            ->visibility('public')
                            ->image()
                            ->required()
                            ->maxSize(1024),
                    ])->columns(2),
                Forms\Components\Section::make('Meta Information')
                    ->schema([
                        TextInput::make('resource_slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->disabled(fn (Get $get) => $get('sync_slug'))
                            ->dehydrated()
                            ->unique(ResourceModel::class, 'resource_slug', ignoreRecord: true),
                        TextInput::make('resource_meta_title')
                            ->label('Meta Title')
                            ->disabled(fn (Get $get) => $get('sync_meta_title'))
                            ->dehydrated()
                            ->required(),
                        Textarea::make('resource_meta_description')
                            ->label('Meta Description')
                            ->disabled(fn (Get $get) => $get('sync_meta_description'))
                            ->dehydrated()
                            ->required(),
                        Textarea::make('resource_meta_keyword')
                            ->label('Meta Keywords')
                            ->placeholder('Comma-separated')
                            ->required(),
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
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => [
                        'primary', 'secondary', 'success', 'danger', 'warning', 'info',
                    ][crc32($state) % 6] ?? 'primary')
                    ->searchable(),
                Tables\Columns\IconColumn::make('active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_by')
                    ->label('Author')
                    ->searchable()
                    ->sortable(),
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
                Tables\Filters\Filter::make('title_search')
                    ->form([
                        Forms\Components\TextInput::make('title')
                            ->label('Search Title')
                            ->placeholder('Enter resource title'),
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
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
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
                Tables\Filters\Filter::make('author_search')
                    ->form([
                        Forms\Components\TextInput::make('created_by')
                            ->label('Search Author')
                            ->placeholder('Enter author name'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['created_by'],
                            fn (Builder $query, $value): Builder => $query->where('created_by', 'like', '%' . $value . '%')
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['created_by'] ?? null) {
                            return 'Searching for author: ' . $data['created_by'];
                        }
                        return null;
                    }),
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
                        TextEntry::make('resource_description')
                            ->label('Short Description')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Resource Content and Media')
                    ->schema([
                        ImageEntry::make('resource_banner')
                            ->label('Banner Image')
                            ->columnSpanFull()
                            ->extraAttributes(['class' => 'w-full h-auto']),
                        ImageEntry::make('resource_thumbnail')
                            ->label('Thumbnail Image')
                            ->columnSpanFull()
                            ->extraAttributes(['class' => 'w-full h-auto']),
                        TextEntry::make('resource_content')
                            ->label('Content')
                            ->html()
                            ->columnSpanFull(),
                        TextEntry::make('resource_url')
                            ->label('Resource URL'),
                    ])
                    ->columns(2),
                Section::make('Meta Information')
                    ->schema([
                        TextEntry::make('resource_meta_title')
                            ->label('Meta Title'),
                        TextEntry::make('resource_slug')
                            ->label('Slug'),
                        TextEntry::make('resource_meta_description')
                            ->label('Meta Description')
                            ->columnSpanFull(),
                        TextEntry::make('resource_meta_keyword')
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
        return [];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResources::route('/'),
            'create' => Pages\CreateResource::route('/create'),
            'edit' => Pages\EditResource::route('/{record}/edit'),
            'view' => Pages\ViewResource::route('/{record}'),
        ];
    }
}