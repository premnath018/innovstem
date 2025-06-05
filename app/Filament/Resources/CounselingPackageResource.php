<?php

namespace App\Filament\Resources;

use App\Filament\Exports\CounselingPackageExport;
use App\Filament\Resources\CounselingPackageResource\Pages;
use App\Models\CounselingPackage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class CounselingPackageResource extends Resource
{
    protected static ?string $model = CounselingPackage::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationLabel = 'Counseling Packages';

    protected static ?string $navigationGroup = 'Career Counseling';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'counseling-packages';

    protected static ?string $recordTitleAttribute = 'package_name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Package Details')
                    ->schema([
                        Forms\Components\Select::make('category')
                            ->label('Category')
                            ->options([
                                'Student' => 'Student',
                                'Parental' => 'Parental',
                                'Teacher' => 'Teacher',
                                'Overall' => 'Overall',
                            ])
                            ->native(false)
                            ->required(),
                        Forms\Components\TextInput::make('package_name')
                            ->label('Package Name')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('price_inr')
                            ->label('Price (INR)')
                            ->required()
                            ->numeric()
                            ->prefix('₹')
                            ->minValue(0),
                        Forms\Components\TextInput::make('duration')
                            ->label('Duration')
                            ->maxLength(255)
                            ->placeholder('e.g., 30 min,  // 3 sessions'),
                        Forms\Components\Textarea::make('includes')
                            ->label('Includes')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('active')
                            ->label('Active')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category')
                    ->label('Category')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-tag'),
                Tables\Columns\TextColumn::make('package_name')
                    ->label('Package Name')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-briefcase'),
                Tables\Columns\TextColumn::make('price_inr')
                    ->label('Price (INR)')
                    ->money('INR')
                    ->sortable()
                    ->icon('heroicon-o-currency-rupee'),
                Tables\Columns\TextColumn::make('duration')
                    ->label('Duration')
                    ->placeholder('N/A')
                    ->sortable()
                    ->icon('heroicon-o-clock'),
                Tables\Columns\IconColumn::make('active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->icon('heroicon-o-calendar')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->icon('heroicon-o-arrow-path')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Category')
                    ->options([
                        'Student' => 'Student',
                        'Parental' => 'Parental',
                        'Teacher' => 'Teacher',
                        'Overall' => 'Overall',
                    ])
                    ->native(false),
                Tables\Filters\TernaryFilter::make('active')
                    ->label('Active')
                    ->trueLabel('Active Packages')
                    ->falseLabel('Inactive Packages')
                    ->native(false),
                Tables\Filters\Filter::make('package_name_search')
                    ->form([
                        Forms\Components\TextInput::make('package_name')
                            ->label('Search Package Name')
                            ->placeholder('Enter package name'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['package_name'],
                            fn (Builder $query, $value): Builder => $query->where('package_name', 'like', '%' . $value . '%')
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['package_name'] ?? null) {
                            return 'Searching for package name: ' . $data['package_name'];
                        }
                        return null;
                    }),
                Tables\Filters\Filter::make('price_range')
                    ->form([
                        Forms\Components\TextInput::make('price_from')
                            ->label('Price From (INR)')
                            ->numeric()
                            ->type('number')
                            ->placeholder('Min price'),
                        Forms\Components\TextInput::make('price_to')
                            ->label('Price To (INR)')
                            ->type('number')
                            ->numeric()
                            ->placeholder('number')
                            ->placeholder('Max price'),
                    ])
                    ->columns(2)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['price_from'],
                                fn (Builder $query, $value): Builder => $query->where('price_inr', '>=', $value),
                            )
                            ->when(
                                $data['price_to'],
                                fn (Builder $query, $value): Builder => $query->where('price_inr', '<=', $value),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['price_from'] ?? null) {
                            $indicators[] = 'Price From: ₹' . $data['price_from'];
                        }
                        if ($data['price_to'] ?? null) {
                            $indicators[] = 'Price To: ₹' . $data['price_to'];
                        }
                        return $indicators;
                    })
                    ->label('Price Range'),
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
                Tables\Filters\SelectFilter::make('duration')
                    ->label('Duration')
                    ->options(function () {
                        return CounselingPackage::distinct()
                            ->pluck('duration')
                            ->filter()
                            ->mapWithKeys(fn ($duration) => [$duration => $duration])
                            ->toArray();
                    })
                    ->native(false)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $value): Builder => $query->where('duration', $value)
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['value'] ?? null) {
                            return 'Duration: ' . $data['value'];
                        }
                        return null;
                    }),
            ])
            ->persistFiltersInSession()
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->color('gray'),
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil'),
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->icon('heroicon-o-trash'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(25);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Package Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('category')
                            ->label('Category')
                            ->icon('heroicon-o-tag'),
                        Infolists\Components\TextEntry::make('package_name')
                            ->label('Package Name')
                            ->weight('bold')
                            ->icon('heroicon-o-briefcase'),
                        Infolists\Components\TextEntry::make('price_inr')
                            ->label('Price (INR)')
                            ->money('INR')
                            ->icon('heroicon-o-currency-rupee'),
                        Infolists\Components\TextEntry::make('duration')
                            ->label('Duration')
                            ->placeholder('N/A')
                            ->icon('heroicon-o-clock'),
                    ])
                    ->columns(2),
                Infolists\Components\Section::make('Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('includes')
                            ->label('Includes')
                            ->html()
                            ->columnSpanFull()
                            ->placeholder('N/A')
                            ->icon('heroicon-o-information-circle'),
                        Infolists\Components\TextEntry::make('active')
                            ->label('Active')
                            ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No')
                            ->badge()
                            ->color(fn ($state) => $state ? 'success' : 'danger')
                            ->icon('heroicon-o-check-circle'),
                    ])
                    ->columns(2),
                Infolists\Components\Section::make('Timestamps')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime()
                            ->icon('heroicon-o-calendar'),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime()
                            ->icon('heroicon-o-arrow-path'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCounselingPackages::route('/'),
            'create' => Pages\CreateCounselingPackage::route('/create'),
            'edit' => Pages\EditCounselingPackage::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}