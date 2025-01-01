<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WebinarResource\Pages;
use App\Filament\Resources\WebinarResource\RelationManagers;
use App\Models\Webinar;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WebinarResource extends Resource
{
    protected static ?string $model = Webinar::class;

    protected static ?string $navigationIcon = 'heroicon-o-video-camera';

    protected static ?string $navigationGroup = 'Content Management System';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('webinar_slug')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('webinar_title')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('webinar_description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('webinar_content')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('webinar_banner')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('webinar_thumbnail')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('category_id')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('created_by')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('webinar_meta_title')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('webinar_meta_keyword')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('webinar_meta_description')
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('webinar_date_time')
                    ->required(),
                Forms\Components\TextInput::make('view_count')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('webinar_date_time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('view_count')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListWebinars::route('/'),
            'create' => Pages\CreateWebinar::route('/create'),
            'edit' => Pages\EditWebinar::route('/{record}/edit'),
        ];
    }
}
