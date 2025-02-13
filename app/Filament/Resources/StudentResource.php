<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationGroup = 'User Management';

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Section::make('Student Details')
                    ->schema([
                        TextInput::make('name')->required(),
                        TextInput::make('mobile')->required()->unique(),
                        Select::make('standard')
                            ->options([
                                'Class 6' => 'Class 6',
                                'Class 7' => 'Class 7',
                                'Class 8' => 'Class 8',
                                'Class 9' => 'Class 9',
                                'Class 10' => 'Class 10',
                                'Class 11' => 'Class 11',
                                'Class 12' => 'Class 12',
                            ])
                            ->required(),
                        TextInput::make('ambition')->nullable(),
                        TextInput::make('parent_no')->nullable(),
                        TextInput::make('age')->numeric()->required(),
                        Select::make('gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                                'other' => 'Other',
                            ])
                            ->required(),
                    ])->columns(2),

                Section::make('Address Details')
                    ->schema([
                        TextInput::make('district')->required(),
                        Textarea::make('address')->rows(3)->required(),
                        TextInput::make('state')->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->label('ID'),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('mobile')->searchable(),
                TextColumn::make('standard')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'Class 6' => 'blue',
                        'Class 7' => 'green',
                        'Class 8' => 'yellow',
                        'Class 9' => 'purple',
                        'Class 10' => 'orange',
                        'Class 11' => 'red',
                        'Class 12' => 'pink',
                        default => 'gray',
                    }),
                TextColumn::make('gender')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'male' => 'blue',
                        'female' => 'pink',
                        'other' => 'gray',
                    }),
                TextColumn::make('district')->sortable()->searchable(),
                TextColumn::make('state')->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('standard')
                    ->options([
                        'Class 6' => 'Class 6',
                        'Class 7' => 'Class 7',
                        'Class 8' => 'Class 8',
                        'Class 9' => 'Class 9',
                        'Class 10' => 'Class 10',
                        'Class 11' => 'Class 11',
                        'Class 12' => 'Class 12',
                    ]),
                SelectFilter::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                        'other' => 'Other',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->modal(),
                Tables\Actions\EditAction::make()->modal(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Grid::make(2)
                    ->schema([
                        TextEntry::make('name')->label('Student Name')->weight('bold'),
                        TextEntry::make('mobile')->label('Mobile'),
                        TextEntry::make('standard')->label('Class')->badge(),
                        TextEntry::make('ambition')->label('Ambition')->hidden(fn ($record) => !$record->ambition),
                        TextEntry::make('parent_no')->label('Parent Mobile')->hidden(fn ($record) => !$record->parent_no),
                        TextEntry::make('age')->label('Age'),
                        TextEntry::make('gender')->label('Gender')->badge(),
                        TextEntry::make('district')->label('District'),
                        TextEntry::make('address')->label('Full Address')->columnSpanFull(),
                        TextEntry::make('state')->label('State'),
                        TextEntry::make('created_at')->label('Registered At')->dateTime(),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
        ];
    }
}
