<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CareerApplicationResource\Pages;
use App\Models\CareerApplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CareerApplicationResource extends Resource
{
    protected static ?string $model = CareerApplication::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Career Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Career Applications';

    protected static ?string $slug = 'career-applications';

    protected static ?string $recordTitleAttribute = 'applicant_name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('career_id')
                    ->label('Career Name')
                    ->relationship('career', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('applicant_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\Textarea::make('cover_letter')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('resume_path')
                    ->label('Resume')
                    ->directory('resumes')
                    ->visibility('public')
                    ->disk('public')
                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                    ->maxSize(5120) // 5MB
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->options([
                        'Pending' => 'Pending',
                        'Under Review' => 'Under Review',
                        'Shortlisted' => 'Shortlisted',
                        'Rejected' => 'Rejected',
                        'Accepted' => 'Accepted',
                    ])
                    ->default('Pending')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('career.title')
                    ->label('Career')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('applicant_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('phone')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'gray',
                        'Under Review' => 'info',
                        'Shortlisted' => 'primary',
                        'Rejected' => 'danger',
                        'Accepted' => 'success',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('resume_path')
                    ->label('Resume')
                    ->formatStateUsing(fn ($state) => $state ? '<a href="' . \Illuminate\Support\Facades\Storage::url($state) . '" target="_blank" class="text-blue-600 hover:underline">View</a>' : '-')
                    ->html()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('career_id')
                    ->label('Career')
                    ->relationship('career', 'title')
                    ->searchable()
                    ->native(false)
                    ->preload(),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Pending' => 'Pending',
                        'Under Review' => 'Under Review',
                        'Shortlisted' => 'Shortlisted',
                        'Rejected' => 'Rejected',
                        'Accepted' => 'Accepted',
                    ])
                    ->native(false),
                Tables\Filters\SelectFilter::make('limit_applicants')
                    ->label('Limit Applicants')
                    ->options([
                        '10' => 'Top 10',
                        '50' => 'Top 50',
                        '75' => 'Top 75',
                        '100' => 'Top 100',
                    ])
                    ->native(false)
                    ->query(function (Builder $query, array $data): Builder {
                        if ($data['value']) {
                            return $query->limit($data['value']);
                        }
                        return $query;
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['value'] ?? null) {
                            return 'Showing top ' . $data['value'] . ' applicants';
                        }
                        return null;
                    }),
                Tables\Filters\Filter::make('applicant_name_search')
                    ->form([
                        Forms\Components\TextInput::make('applicant_name')
                            ->label('Search Applicant Name')
                            ->placeholder('Enter applicant name'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['applicant_name'],
                            fn (Builder $query, $value): Builder => $query->where('applicant_name', 'like', '%' . $value . '%')
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['applicant_name'] ?? null) {
                            return 'Searching for applicant: ' . $data['applicant_name'];
                        }
                        return null;
                    }),
                Tables\Filters\SelectFilter::make('resume_availability')
                    ->label('Resume Availability')
                    ->native(false)
                    ->options([
                        'with_resume' => 'With Resume',
                        'without_resume' => 'Without Resume',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if ($data['value'] === 'with_resume') {
                            return $query->whereNotNull('resume_path');
                        } elseif ($data['value'] === 'without_resume') {
                            return $query->whereNull('resume_path');
                        }
                        return $query;
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['value'] === 'with_resume') {
                            return 'Showing applicants with resumes';
                        } elseif ($data['value'] === 'without_resume') {
                            return 'Showing applicants without resumes';
                        }
                        return null;
                    }),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Applied From')
                            ->native(false),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Applied Until')
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
                            $indicators[] = 'Applied From: ' . \Carbon\Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators[] = 'Applied Until: ' . \Carbon\Carbon::parse($data['created_until'])->toFormattedDateString();
                        }
                        return $indicators;
                    })
                    ->label('Applied Date Range'),
            ])
            ->persistFiltersInSession()
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCareerApplications::route('/'),
            'create' => Pages\CreateCareerApplication::route('/create'),
            'edit' => Pages\EditCareerApplication::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}