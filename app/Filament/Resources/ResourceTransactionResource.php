<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResourceTransactionResource\Pages;
use App\Models\ResourceTransaction;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Infolist;


class ResourceTransactionResource extends Resource
{
    protected static ?string $model = ResourceTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-rupee';

    protected static ?string $navigationGroup = 'Content Management System';

    protected static ?int $navigationSort = 8;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Transaction Details')
                    ->schema([
                        Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->required()
                            ->native(false)
                            ->searchable()
                            ->preload(),
                        Select::make('resource_id')
                            ->label('Resource')
                            ->relationship('resource', 'title')
                            ->required()
                            ->native(false)
                            ->searchable()
                            ->preload(),
                        TextInput::make('razorpay_order_id')
                            ->label('Razorpay Order ID')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('razorpay_payment_id')
                            ->label('Razorpay Payment ID')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('razorpay_signature')
                            ->label('Razorpay Signature')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('amount')
                            ->label('Amount (INR)')
                            ->required()
                            ->numeric()
                            ->prefix('₹'),
                        TextInput::make('currency')
                            ->label('Currency')
                            ->required()
                            ->default('INR')
                            ->maxLength(3),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'failed' => 'Failed',
                            ])
                            ->required()
                            ->native(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('resource.title')
                    ->label('Resource')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                    ])
                    ->native(false),
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->native(false)
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('resource_id')
                    ->label('Resource')
                    ->relationship('resource', 'title')
                    ->native(false)
                    ->searchable()
                    ->preload(),
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
                Section::make('Transaction Details')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('User'),
                        TextEntry::make('resource.title')
                            ->label('Resource'),
                        TextEntry::make('amount')
                            ->label('Amount')
                            ->money('INR'),
                        TextEntry::make('currency')
                            ->label('Currency'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'paid' => 'success',
                                'pending' => 'warning',
                                'failed' => 'danger',
                            }),
                    ])->columns(2),
                Section::make('Razorpay Details')
                    ->schema([
                        TextEntry::make('razorpay_order_id')
                            ->label('Razorpay Order ID'),
                        TextEntry::make('razorpay_payment_id')
                            ->label('Razorpay Payment ID'),
                        TextEntry::make('razorpay_signature')
                            ->label('Razorpay Signature')
                            ->columnSpanFull(),
                    ])->columns(2),
                Section::make('Timestamps')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime(),
                    ])->columns(2),
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
            'index' => Pages\ListResourceTransactions::route('/'),
            'create' => Pages\CreateResourceTransaction::route('/create'),
            'edit' => Pages\EditResourceTransaction::route('/{record}/edit'),
            'view' => Pages\ViewResourceTransaction::route('/{record}'),
        ];
    }
}