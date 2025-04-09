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
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Pending' => 'Pending',
                        'Under Review' => 'Under Review',
                        'Shortlisted' => 'Shortlisted',
                        'Rejected' => 'Rejected',
                        'Accepted' => 'Accepted',
                    ]),
                Tables\Filters\SelectFilter::make('career_id')
                    ->label('Careers')
                    ->relationship('career', 'title'),
            ])
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