<?php

namespace App\Filament\Resources;

use App\Filament\Exports\AppointmentExport;
use App\Filament\Resources\AppointmentResource\Pages;
use App\Models\Appointment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Appointments';

    protected static ?string $navigationGroup = 'Career Counseling';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'appointments';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Appointment Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('mobile_number')
                            ->label('Mobile Number')
                            ->required()
                            ->maxLength(15)
                            ->tel(),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->required()
                            ->email()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('class')
                            ->label('Class')
                            ->maxLength(20),
                        Forms\Components\Select::make('gender')
                            ->label('Gender')
                            ->options([
                                'Male' => 'Male',
                                'Female' => 'Female',
                                'Other' => 'Other',
                            ])
                            ->native(false),
                        Forms\Components\TextInput::make('ambition')
                            ->label('Ambition')
                            ->maxLength(255),
                        Forms\Components\Select::make('user_type')
                            ->label('User Type')
                            ->options([
                                'Student' => 'Student',
                                'Parent' => 'Parent',
                                'Teacher' => 'Teacher',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\Select::make('appointment_status')
                            ->label('Appointment Status')
                            ->options([
                                'Booked' => 'Booked',
                                'Attended' => 'Attended',
                                'Cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('Booked')
                            ->native(false),
                        Forms\Components\Select::make('package_id')
                            ->label('Counseling Package')
                            ->relationship('package', 'package_name', fn (Builder $query) => $query->where('active', true))
                            ->required()
                            ->preload()
                            ->searchable()
                            ->native(false),
                        Forms\Components\Select::make('slot_id')
                            ->label('Slot')
                            ->relationship('slot', 'slot_date', fn (Builder $query) => $query->where('is_active', true))
                            ->getOptionLabelFromRecordUsing(fn ($record) => \Carbon\Carbon::parse($record->slot_date)->toFormattedDateString() . ' ' . \Carbon\Carbon::parse($record->start_time)->format('h:i A') . ' - ' . \Carbon\Carbon::parse($record->end_time)->format('h:i A'))
                            ->required()
                            ->preload()
                            ->searchable()
                            ->native(false),
                        Forms\Components\TextInput::make('transaction_id')
                            ->label('Transaction ID')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('amount_paid')
                            ->label('Amount Paid (INR)')
                            ->numeric()
                            ->prefix('₹')
                            ->minValue(0),
                        Forms\Components\Select::make('payment_status')
                            ->label('Payment Status')
                            ->options([
                                'Pending' => 'Pending',
                                'Paid' => 'Paid',
                                'Failed' => 'Failed',
                            ])
                            ->default('Pending')
                            ->native(false),
                        Forms\Components\TextInput::make('ack')
                            ->label('Acknowledgment Number')
                            ->maxLength(9)
                            ->disabled() // Read-only, auto-generated
                            ->dehydrated(false), // Prevent saving manual input
                        Forms\Components\Textarea::make('note')
                            ->label('Note')
                            ->maxLength(65535)
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
                Tables\Columns\TextColumn::make('ack')
                    ->label('Ack Number')
                    ->searchable()
                    ->sortable()
                    ->placeholder('N/A')
                    ->icon('heroicon-o-identification'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user'),
                Tables\Columns\TextColumn::make('mobile_number')
                    ->label('Mobile Number')
                    ->searchable()
                    ->icon('heroicon-o-phone'),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->icon('heroicon-o-envelope'),
                Tables\Columns\TextColumn::make('user_type')
                    ->label('User Type')
                    ->sortable()
                    ->icon('heroicon-o-identification'),
                Tables\Columns\TextColumn::make('package.package_name')
                    ->label('Package')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-briefcase'),
                Tables\Columns\TextColumn::make('slot.slot_date')
                    ->label('Slot Date')
                    ->date()
                    ->sortable()
                    ->icon('heroicon-o-calendar'),
                Tables\Columns\TextColumn::make('slot.start_time')
                    ->label('Slot Time')
                    ->formatStateUsing(fn ($state, $record) => \Carbon\Carbon::parse($record->slot->start_time)->format('h:i A') . ' - ' . \Carbon\Carbon::parse($record->slot->end_time)->format('h:i A'))
                    ->sortable()
                    ->icon('heroicon-o-clock'),
                Tables\Columns\TextColumn::make('appointment_status')
                    ->label('Appointment Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Booked' => 'info',
                        'Attended' => 'success',
                        'Cancelled' => 'danger',
                    })
                    ->sortable()
                    ->icon('heroicon-o-clipboard-document-check'),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Payment Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Paid' => 'success',
                        'Failed' => 'danger',
                    })
                    ->sortable()
                    ->icon('heroicon-o-banknotes'),
                Tables\Columns\IconColumn::make('active')
                    ->label('Active')
                    ->sortable()
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->icon('heroicon-o-calendar'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_type')
                    ->options([
                        'Student' => 'Student',
                        'Parent' => 'Parent',
                        'Teacher' => 'Teacher',
                    ])
                    ->label('User Type')
                    ->native(false),
                Tables\Filters\SelectFilter::make('appointment_status')
                    ->options([
                        'Booked' => 'Booked',
                        'Attended' => 'Attended',
                        'Cancelled' => 'Cancelled',
                    ])
                    ->label('Appointment Status')
                    ->native(false),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'Pending' => 'Pending',
                        'Paid' => 'Paid',
                        'Failed' => 'Failed',
                    ])
                    ->label('Payment Status')
                    ->native(false),
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
                Tables\Filters\Filter::make('slot_date')
                    ->form([
                        Forms\Components\DatePicker::make('slot_date_from')
                            ->label('Slot Date From')
                            ->native(false),
                        Forms\Components\DatePicker::make('slot_date_until')
                            ->label('Slot Date Until')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['slot_date_from'],
                                fn (Builder $query, $date): Builder => $query->whereHas('slot', fn (Builder $q) => $q->whereDate('slot_date', '>=', $date))
                            )
                            ->when(
                                $data['slot_date_until'],
                                fn (Builder $query, $date): Builder => $query->whereHas('slot', fn (Builder $q) => $q->whereDate('slot_date', '<=', $date))
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['slot_date_from'] ?? null) {
                            $indicators[] = 'Slot Date From: ' . \Carbon\Carbon::parse($data['slot_date_from'])->toFormattedDateString();
                        }
                        if ($data['slot_date_until'] ?? null) {
                            $indicators[] = 'Slot Date Until: ' . \Carbon\Carbon::parse($data['slot_date_until'])->toFormattedDateString();
                        }
                        return $indicators;
                    })
                    ->label('Slot Date Range'),
            ])
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
                Infolists\Components\Section::make('Personal Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Name')
                            ->icon('heroicon-o-user'),
                        Infolists\Components\TextEntry::make('mobile_number')
                            ->label('Mobile Number')
                            ->icon('heroicon-o-phone'),
                        Infolists\Components\TextEntry::make('email')
                            ->label('Email')
                            ->icon('heroicon-o-envelope'),
                        Infolists\Components\TextEntry::make('class')
                            ->label('Class')
                            ->placeholder('N/A')
                            ->icon('heroicon-o-academic-cap'),
                        Infolists\Components\TextEntry::make('gender')
                            ->label('Gender')
                            ->placeholder('N/A')
                            ->icon('heroicon-o-user-circle'),
                        Infolists\Components\TextEntry::make('ambition')
                            ->label('Ambition')
                            ->placeholder('N/A')
                            ->icon('heroicon-o-star'),
                        Infolists\Components\TextEntry::make('user_type')
                            ->label('User Type')
                            ->icon('heroicon-o-identification'),
                    ])
                    ->columns(3),
                Infolists\Components\Section::make('Appointment Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('package.package_name')
                            ->label('Package')
                            ->icon('heroicon-o-briefcase'),
                        Infolists\Components\TextEntry::make('slot.slot_date')
                            ->label('Slot Date')
                            ->date()
                            ->icon('heroicon-o-calendar'),
                        Infolists\Components\TextEntry::make('slot.start_time')
                            ->label('Slot Time')
                            ->formatStateUsing(fn ($state, $record) => \Carbon\Carbon::parse($record->slot->start_time)->format('h:i A') . ' - ' . \Carbon\Carbon::parse($record->slot->end_time)->format('h:i A'))
                            ->icon('heroicon-o-clock'),
                        Infolists\Components\TextEntry::make('appointment_status')
                            ->label('Appointment Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Booked' => 'info',
                                'Attended' => 'success',
                                'Cancelled' => 'danger',
                            })
                            ->icon('heroicon-o-clipboard-document-check'),
                        Infolists\Components\TextEntry::make('ack')
                            ->label('Acknowledgment Number')
                            ->placeholder('N/A')
                            ->icon('heroicon-o-identification'),
                    ])
                    ->columns(3),
                Infolists\Components\Section::make('Payment Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('transaction_id')
                            ->label('Transaction ID')
                            ->placeholder('N/A')
                            ->icon('heroicon-o-receipt-percent'),
                        Infolists\Components\TextEntry::make('amount_paid')
                            ->label('Amount Paid (INR)')
                            ->money('INR')
                            ->placeholder('N/A')
                            ->icon('heroicon-o-currency-rupee'),
                        Infolists\Components\TextEntry::make('payment_status')
                            ->label('Payment Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Pending' => 'warning',
                                'Paid' => 'success',
                                'Failed' => 'danger',
                            })
                            ->icon('heroicon-o-banknotes'),
                    ])
                    ->columns(3),
                Infolists\Components\Section::make('Additional Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('note')
                            ->label('Note')
                            ->placeholder('N/A')
                            ->columnSpanFull()
                            ->icon('heroicon-o-chat-bubble-left'),
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
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}