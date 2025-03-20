<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WebinarResource\Pages;
use App\Models\Webinar;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
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
use Illuminate\Support\Str;
use Filament\Forms\Get;


class WebinarResource extends Resource
{
    protected static ?string $model = Webinar::class;

    protected static ?string $navigationIcon = 'heroicon-o-video-camera';

    protected static ?string $navigationGroup = 'Content Management System';

    protected static ?int $navigationSort = 6;


    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Webinar Details')
                    ->schema([
                        TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set, $get) {
                                if ($get('sync_slug')) {
                                    $set('webinar_slug', Str::slug($state));
                                }
                                if ($get('sync_meta_title')) {
                                    $set('webinar_meta_title', $state);
                                }
                            }),
                        Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name')
                            ->native(false)
                            ->required(),
                        TextInput::make('webinar_link')
                        ->label('Webinar URL')
                        ->required(),
                        Textarea::make('webinar_description')
                            ->label('Description')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set, $get) {
                                if ($get('sync_meta_description')) {
                                    $set('webinar_meta_description', $state);
                                }
                            }),
                        DateTimePicker::make('webinar_date_time')
                            ->label('Date and Time')
                            ->required(),
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

                Forms\Components\Section::make('Webinar Media & Content')
                    ->schema([
                        RichEditor::make('webinar_content')
                            ->label('Content or Webinar Link')
                            ->required()
                            ->columnSpanFull(),
                        FileUpload::make('webinar_banner')
                            ->label('Banner Image')
                            ->directory('webinars/banners')
                            ->visibility('public')
                            ->image()
                            ->required()
                            ->maxSize(2048),
                        FileUpload::make('webinar_thumbnail')
                            ->label('Thumbnail Image')
                            ->directory('webinars/thumbnails')
                            ->visibility('public')
                            ->image()
                            ->required()
                            ->maxSize(1024),
                    ])->columns(2),

                Forms\Components\Section::make('Meta Information')
                    ->schema([
                        TextInput::make('webinar_slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->disabled(fn (Get $get) => $get('sync_slug'))
                            ->dehydrated()
                            ->unique(Webinar::class, 'webinar_slug',ignoreRecord: true),
                        TextInput::make('webinar_meta_title')
                            ->label('Meta Title')
                            ->disabled(fn (Get $get) => $get('sync_meta_title'))
                            ->dehydrated()
                            ->required(),
                        Textarea::make('webinar_meta_description')
                            ->label('Meta Description')
                            ->disabled(fn (Get $get) => $get('sync_meta_description'))
                            ->dehydrated()
                            ->required(),
                        Textarea::make('webinar_meta_keyword')
                            ->label('Meta Keywords')
                            ->placeholder('Comma-separated')
                            ->required(),
                        TextInput::make('created_by')
                            ->label('Author')
                            ->required(),
                        TextInput::make('view_count')
                            ->label('View Count')
                            ->required()
                            ->numeric()
                            ->default(0),
                            Select::make('active')
                            ->label('Active')
                            ->options([ 0 => 'Inactive', 1 => 'Active'])
                            ->required()
                            ->native(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => [
                        'primary', 'secondary', 'success', 'danger', 'warning', 'info',
                    ][crc32($state) % 6] ?? 'primary') // Dynamically assign color
                    ->searchable(),
                Tables\Columns\TextColumn::make('webinar_date_time')
                    ->label('Date and Time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('view_count')
                    ->label('Views')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('attendance_count')
                ->label('Enrollment')
                ->numeric()
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
                        TextEntry::make('webinar_date_time')
                            ->label('Date and Time'),
                        TextEntry::make('webinar_description')
                            ->label('Short Description'),
                    ])
                    ->columns(2),

                Section::make('Webinar Content and Media')
                    ->schema([
                        ImageEntry::make('webinar_banner')
                            ->label('Banner Image')
                            ->size(400),
                        ImageEntry::make('webinar_thumbnail')
                            ->label('Thumbnail Image')
                            ->size(200),
                        TextEntry::make('webinar_content')
                            ->label('Content')
                            ->html()
                            ->columnSpanFull(),
                        TextEntry::make('webinar_url')
                            ->label('Resource URL'),
                    ])
                    ->columns(2),

                Section::make('Meta Information')
                    ->schema([
                        TextEntry::make('webinar_meta_title')
                            ->label('Meta Title'),
                        TextEntry::make('webinar_slug')
                            ->label('Slug'),
                        TextEntry::make('webinar_meta_description')
                            ->label('Meta Description')
                            ->columnSpanFull(),
                        TextEntry::make('webinar_meta_keyword')
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
            // Define any relations here if needed
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWebinars::route('/'),
            'create' => Pages\CreateWebinar::route('/create'),
            'edit' => Pages\EditWebinar::route('/{record}/edit'),
            'view' => Pages\ViewWebinar::route('/{record}'),

        ];
    }
}
