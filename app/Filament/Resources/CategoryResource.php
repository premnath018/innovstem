<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;


class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Content Management System';

    protected static ?int $navigationSort = 1;


    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Section::make('General Information')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                    $set('slug', Str::slug($state));
                            })
                        ->maxLength(255),
                        Select::make('active')
                        ->label('Active')
                        ->options([ 0 => 'Inactive', 1 => 'Active'])
                        ->required()
                        ->native(false),
                    TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->disabled()
                    ->maxLength(255)
                    ->dehydrated(),
                    Forms\Components\Textarea::make('short_description')
                        ->columnSpanFull(),
                ]),
            Forms\Components\Section::make('Details')
                ->schema([
                    Forms\Components\Textarea::make('long_description')
                        ->columnSpanFull(),
                    Forms\Components\FileUpload::make('image_url')
                    ->label('Category Image')
                    ->image()
                    ->directory('categories') // Directory in `storage/app/public/categories`
                    ->visibility('public') // Ensure file is publicly accessible
                    ->maxSize(1024) // 1MB limit
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg','image/webp']),
               ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('Index')->rowIndex(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('short_description'),
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

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
