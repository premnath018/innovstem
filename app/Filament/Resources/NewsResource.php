<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsResource\Pages;
use App\Filament\Resources\NewsResource\RelationManagers;
use App\Models\News;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;
    protected static ?string $navigationGroup = 'Content Management System';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'News';
    protected static ?string $slug = 'news';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('News Content')
                    ->description('Main content of the news article')
                    ->schema([
                        RichEditor::make('content')
                            ->label('News Content')
                            ->required()
                            ->columnSpanFull(),
                    ]),
                Section::make('Settings')
                    ->description('Configure news visibility and priority')
                    ->schema([
                        Toggle::make('active')
                            ->label('Active')
                            ->default(true)
                            ->inline(false),
                        TextInput::make('priority')
                            ->label('Priority')
                            ->numeric()
                            ->default(0)
                            ->helperText('Higher numbers appear first'),
                        Toggle::make('latest_news')
                            ->label('Mark as Latest')
                            ->default(false)
                            ->inline(false)
                            ->helperText('Marks this as a featured latest news item'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfoSection::make('News Details')
                    ->schema([
                        TextEntry::make('content')
                            ->label('Content')
                            ->html(),
                        TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime(),
                    ]),
                InfoSection::make('Status')
                    ->schema([
                        TextEntry::make('active')
                            ->label('Active')
                            ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No'),
                        TextEntry::make('priority')
                            ->label('Priority'),
                        TextEntry::make('latest_news')
                            ->label('Latest News')
                            ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('content')
                    ->label('News Content')
                    ->html() // Enables HTML rendering
                    ->limit(50) // Still limits characters to prevent overwhelming the table
                    ->formatStateUsing(function ($state) {
                        // Strip tags for plain text preview while preserving basic formatting
                        return strip_tags($state, '<p><br><strong><em><ul><li>');
                    }),
                IconColumn::make('active')
                    ->label('Active')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                TextColumn::make('priority')
                    ->label('Priority')
                    ->sortable(),
                IconColumn::make('latest_news')
                    ->label('Latest')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }
}