<?php

namespace App\Filament\Resources\CareerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ApplicationsRelationManager extends RelationManager
{
    protected static string $relationship = 'applications';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
                    ->maxLength(65535),
                Forms\Components\FileUpload::make('resume_path')
                    ->label('Resume')
                    ->directory('resumes')
                    ->visibility('public')
                    ->disk('public')
                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                    ->maxSize(5120),
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

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('applicant_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone'),
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
                    ->html(),
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
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}