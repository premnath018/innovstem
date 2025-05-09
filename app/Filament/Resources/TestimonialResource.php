<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestimonialResource\Pages;
use App\Filament\Resources\TestimonialResource\RelationManagers;
use App\Models\Testimonial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static ?int $navigationSort = 9;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center';

    protected static ?string $navigationGroup = 'Content Management System';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('testimonial_name')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('designation')
                ->required()
                ->maxLength(255),
            Forms\Components\Textarea::make('testimonial')
                ->required()
                ->rows(5),
            Forms\Components\FileUpload::make('photo_url')
                ->label('Photo')
                ->directory('testimonials')
                ->image()
                ->maxSize(2048),
            Forms\Components\Select::make('active')
                ->label('Active')
                ->options([ false => 'Inactive', true => 'Active'])
                ->required()
                ->native(false),
            
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo_url')->label('Photo')->circular(),
                Tables\Columns\TextColumn::make('testimonial_name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('designation')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('testimonial')->limit(50)->wrap(),
                Tables\Columns\IconColumn::make('active')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
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
            'index' => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit' => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
