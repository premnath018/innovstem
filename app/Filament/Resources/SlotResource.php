<?php

namespace App\Filament\Resources;

use App\Filament\Exports\SlotExport;
use App\Filament\Resources\SlotResource\Pages;
use App\Models\Slot;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Actions;
use Illuminate\Support\Carbon;

class SlotResource extends Resource
{
    protected static ?string $model = Slot::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Slots';


    protected static ?string $navigationGroup = 'Career Counseling';

    protected static ?int $navigationSort = 2;


    protected static ?string $slug = 'slots';

    protected static ?string $recordTitleAttribute = 'slot_date';

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Forms\Components\Section::make('Slot Details')
                    ->schema([
                        Forms\Components\Select::make('day_of_week')
                            ->label('Day of Week')
                            ->options([
                                'Monday' => 'Monday',
                                'Tuesday' => 'Tuesday',
                                'Wednesday' => 'Wednesday',
                                'Thursday' => 'Thursday',
                                'Friday' => 'Friday',
                                'Saturday' => 'Saturday',
                                'Sunday' => 'Sunday',
                            ])
                            ->required(),
                        Forms\Components\DatePicker::make('slot_date')
                            ->label('Slot Date')
                            ->required()
                            ->minDate(now())
                            ->native(false),
                        Forms\Components\TimePicker::make('start_time')
                            ->label('Start Time')
                            ->required()
                            ->seconds(false),
                        Forms\Components\TimePicker::make('end_time')
                            ->label('End Time')
                            ->required()
                            ->seconds(false)
                            ->after('start_time'),
                        Forms\Components\Toggle::make('is_active')
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
                Tables\Columns\TextColumn::make('day_of_week')
                    ->label('Day of Week')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-calendar-days'),
                Tables\Columns\TextColumn::make('slot_date')
                    ->label('Slot Date')
                    ->date()
                    ->sortable()
                    ->icon('heroicon-o-calendar'),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Start Time')
                    ->time('h:i A')
                    ->sortable()
                    ->icon('heroicon-o-clock'),
                Tables\Columns\TextColumn::make('end_time')
                    ->label('End Time')
                    ->time('h:i A')
                    ->sortable()
                    ->icon('heroicon-o-clock'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->sortable()
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->icon('heroicon-o-calendar'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->icon('heroicon-o-arrow-path'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('day_of_week')
                    ->options([
                        'Monday' => 'Monday',
                        'Tuesday' => 'Tuesday',
                        'Wednesday' => 'Wednesday',
                        'Thursday' => 'Thursday',
                        'Friday' => 'Friday',
                        'Saturday' => 'Saturday',
                        'Sunday' => 'Sunday',
                    ])
                    ->label('Day of Week')
                    ->native(false),
                Tables\Filters\Filter::make('slot_date')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('Date From')
                            ->native(false),
                        Forms\Components\DatePicker::make('date_until')
                            ->label('Date Until')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('slot_date', '>=', $date)
                            )
                            ->when(
                                $data['date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('slot_date', '<=', $date)
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['date_from'] ?? null) {
                            $indicators[] = 'Date From: ' . \Carbon\Carbon::parse($data['date_from'])->toFormattedDateString();
                        }
                        if ($data['date_until'] ?? null) {
                            $indicators[] = 'Date Until: ' . \Carbon\Carbon::parse($data['date_until'])->toFormattedDateString();
                        }
                        return $indicators;
                    })
                    ->label('Slot Date Range'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->trueLabel('Active Slots')
                    ->falseLabel('Inactive Slots')
                    ->native(false),
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
            ->headerActions([
                Tables\Actions\Action::make('generate_slots')
                    ->label('Generate Slots')
                    ->icon('heroicon-o-plus-circle')
                    ->form([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Start Date')
                            ->required()
                            ->minDate(now())
                            ->native(false),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('End Date')
                            ->required()
                            ->minDate(now())
                            ->after('start_date')
                            ->native(false),
                        Forms\Components\Select::make('times')
                            ->label('Select Times')
                            ->multiple()
                            ->required()
                            ->options([
                                '08:00:00' => '08:00 AM',
                                '09:00:00' => '09:00 AM',
                                '10:00:00' => '10:00 AM',
                                '11:00:00' => '11:00 AM',
                                '12:00:00' => '12:00 PM',
                                '13:00:00' => '01:00 PM',
                                '14:00:00' => '02:00 PM',
                                '15:00:00' => '03:00 PM',
                                '16:00:00' => '04:00 PM',
                                '17:00:00' => '05:00 PM',
                                '18:00:00' => '06:00 PM',
                                '19:00:00' => '07:00 PM',
                                '20:00:00' => '08:00 PM',
                                '21:00:00' => '09:00 PM',
                                '22:00:00' => '10:00 PM',
                            ])
                            ->native(false),
                    ])
                    ->action(function (array $data) {
                        $startDate = Carbon::parse($data['start_date']);
                        $endDate = Carbon::parse($data['end_date']);
                        $times = $data['times'];

                        // Iterate through each date in the range
                        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                            $dayOfWeek = $date->format('l');

                            // Create a slot for each selected time
                            foreach ($times as $time) {
                                $startTime = Carbon::parse($time);
                                $endTime = $startTime->copy()->addHour();

                                Slot::create([
                                    'day_of_week' => $dayOfWeek,
                                    'slot_date' => $date,
                                    'start_time' => $startTime->format('H:i:s'),
                                    'end_time' => $endTime->format('H:i:s'),
                                    'is_active' => true,
                                ]);
                            }
                        }
                    })
                    ->successNotificationTitle('Slots generated successfully'),
            ])
            ->defaultPaginationPageOption(10);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Slot Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('day_of_week')
                            ->label('Day of Week')
                            ->icon('heroicon-o-calendar-days'),
                        Infolists\Components\TextEntry::make('slot_date')
                            ->label('Slot Date')
                            ->date()
                            ->icon('heroicon-o-calendar'),
                        Infolists\Components\TextEntry::make('start_time')
                            ->label('Start Time')
                            ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('h:i A'))
                            ->icon('heroicon-o-clock'),
                        Infolists\Components\TextEntry::make('end_time')
                            ->label('End Time')
                            ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('h:i A'))
                            ->icon('heroicon-o-clock'),
                        Infolists\Components\TextEntry::make('is_active')
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
            'index' => Pages\ListSlots::route('/'),
            'create' => Pages\CreateSlot::route('/create'),
            'edit' => Pages\EditSlot::route('/{record}/edit'),
        ];
    }


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->orderBy('slot_date', 'asc')
            ->orderBy('start_time', 'asc');
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}