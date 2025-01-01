<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResourceResource\Pages;
use App\Filament\Resources\ResourceResource\RelationManagers;
use App\Models\Resource as ResourceModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ResourceResource extends Resource
{
    protected static ?string $model = ResourceModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Content Management System';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('resource_slug')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('resource_title')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('resource_url')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('resource_description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('resource_content')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('resource_banner')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('resource_thumbnail')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('category_id')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('created_by')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('resource_meta_title')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('resource_meta_keyword')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('resource_meta_description')
                    ->columnSpanFull(),
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
            'index' => Pages\ListResources::route('/'),
            'create' => Pages\CreateResource::route('/create'),
            'edit' => Pages\EditResource::route('/{record}/edit'),
        ];
    }
}
