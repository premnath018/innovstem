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
                    ->html()
                    ->limit(50)
                    ->formatStateUsing(function ($state) {
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
            ->filters([
                Tables\Filters\Filter::make('content_search')
                    ->form([
                        Forms\Components\TextInput::make('content')
                            ->label('Search Content')
                            ->placeholder('Enter keywords'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['content'],
                            fn (Builder $query, $value): Builder => $query->where('content', 'like', '%' . $value . '%')
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['content'] ?? null) {
                            return 'Searching for content: ' . $data['content'];
                        }
                        return null;
                    }),
                Tables\Filters\TernaryFilter::make('active')
                    ->label('Active'),
                Tables\Filters\TernaryFilter::make('latest_news')
                    ->label('Latest News'),
                Tables\Filters\Filter::make('priority_range')
                    ->form([
                        Forms\Components\TextInput::make('priority_from')
                            ->label('Priority From')
                            ->numeric()
                            ->placeholder('Min priority'),
                        Forms\Components\TextInput::make('priority_to')
                            ->label('Priority To')
                            ->numeric()
                            ->placeholder('Max priority'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['priority_from'],
                                fn (Builder $query, $value): Builder => $query->where('priority', '>=', $value)
                            )
                            ->when(
                                $data['priority_to'],
                                fn (Builder $query, $value): Builder => $query->where('priority', '<=', $value)
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['priority_from'] ?? null) {
                            $indicators[] = 'Priority From: ' . $data['priority_from'];
                        }
                        if ($data['priority_to'] ?? null) {
                            $indicators[] = 'Priority To: ' . $data['priority_to'];
                        }
                        return $indicators;
                    })
                    ->label('Priority Range'),
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
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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