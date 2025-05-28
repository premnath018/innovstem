<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
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
use Illuminate\Database\Eloquent\Builder;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationGroup = 'Student Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Section::make('Student Details')
                    ->schema([
                        TextInput::make('name')->required(),
                        TextInput::make('mobile')->required()->unique(),
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->native(false),
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
                            ->required()
                            ->native(false),
                        TextInput::make('ambition')->nullable(),
                        TextInput::make('parent_no')->nullable(),
                        TextInput::make('age')->numeric()->required(),
                        Select::make('gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                                'other' => 'Other',
                            ])
                            ->required()
                            ->native(false),
                        Select::make('active')
                            ->label('Active')
                            ->options([0 => 'Inactive', 1 => 'Active'])
                            ->required()
                            ->native(false),
                    ])->columns(2),
                Section::make('Address Details')
                    ->schema([
                        TextInput::make('district')->required(),
                        TextInput::make('city'),
                        TextInput::make('pincode'),
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
                    ->color(fn (string $state): string => [
                        'primary', 'secondary', 'success', 'danger', 'warning', 'info',
                    ][crc32($state) % 6] ?? 'primary'),
                TextColumn::make('gender')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'male' => 'primary',
                        'female' => 'secondary',
                        'other' => 'warning',
                    }),
                TextColumn::make('district')->sortable()->searchable(),
                TextColumn::make('state')->sortable(),
                TextColumn::make('active')
                    ->label('Active')
                    ->formatStateUsing(fn ($state) => $state ? 'Active' : 'Inactive')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'danger'),
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
                    ])
                    ->native(false),
                SelectFilter::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                        'other' => 'Other',
                    ])
                    ->native(false),
                Tables\Filters\Filter::make('name_search')
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->label('Search Name')
                            ->placeholder('Enter student name'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['name'],
                            fn (Builder $query, $value): Builder => $query->where('name', 'like', '%' . $value . '%')
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['name'] ?? null) {
                            return 'Searching for name: ' . $data['name'];
                        }
                        return null;
                    }),
                SelectFilter::make('active')
                    ->label('Status')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ])
                    ->native(false),
                Tables\Filters\Filter::make('age_range')
                    ->form([
                        Forms\Components\TextInput::make('age_from')
                            ->label('Age From')
                            ->numeric()
                            ->placeholder('Min age'),
                        Forms\Components\TextInput::make('age_to')
                            ->label('Age To')
                            ->numeric()
                            ->placeholder('Max age'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['age_from'],
                                fn (Builder $query, $value): Builder => $query->where('age', '>=', $value)
                            )
                            ->when(
                                $data['age_to'],
                                fn (Builder $query, $value): Builder => $query->where('age', '<=', $value)
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['age_from'] ?? null) {
                            $indicators[] = 'Age From: ' . $data['age_from'];
                        }
                        if ($data['age_to'] ?? null) {
                            $indicators[] = 'Age To: ' . $data['age_to'];
                        }
                        return $indicators;
                    })
                    ->label('Age Range'),
                SelectFilter::make('district')
                    ->label('District')
                    ->options(function () {
                        return Student::distinct()
                            ->pluck('district')
                            ->filter()
                            ->mapWithKeys(fn ($district) => [$district => $district])
                            ->toArray();
                    })
                    ->native(false)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $value): Builder => $query->where('district', $value)
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['value'] ?? null) {
                            return 'District: ' . $data['value'];
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
            ->filtersFormColumns(2)
            ->persistFiltersInSession()
            ->actions([
                Tables\Actions\ViewAction::make()->modal(),
                Tables\Actions\EditAction::make()->modal(),
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
                        TextEntry::make('active')
                            ->label('Active')
                            ->formatStateUsing(fn ($state) => $state ? 'Active' : 'Inactive')
                            ->badge()
                            ->color(fn ($state) => $state ? 'success' : 'danger'),
                        TextEntry::make('address')->label('Full Address')->columnSpanFull(),
                        TextEntry::make('state')->label('State'),
                        TextEntry::make('created_at')->label('Registered At')->dateTime(),
                    ]),
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
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