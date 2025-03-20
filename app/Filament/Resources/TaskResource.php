<?php

namespace App\Filament\Resources;

use App\Enums\TaskStatus;
use App\Filament\Resources\TaskResource\Pages;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $navigationLabel = 'Tasks';

    protected static ?string $slug = 'tasks';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        $isAdmin = Auth::user()->hasRole('admin') || Auth::user()->hasRole('Super Admin');
        $userId = Auth::id();
        $record = $form->getRecord(); // The task being edited (null on create)

        return $form
            ->schema([
                Forms\Components\Section::make('Task Details')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->disabled(fn () => !$isAdmin && $record !== null),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull()
                            ->disabled(fn () => !$isAdmin && $record !== null),
                        Forms\Components\Select::make('assigned_to')
                            ->label('Assign To')
                            ->relationship('assignedTo', 'name')
                            ->preload()
                            ->searchable()
                            ->visible($isAdmin)
                            ->required(fn () => $isAdmin)
                            ->disabled(fn () => !$isAdmin && $record !== null),
                        Forms\Components\ToggleButtons::make('status')
                            ->inline()
                            ->options(function () use ($isAdmin, $record, $userId) {
                                if ($isAdmin) {
                                    // Admins can set all statuses
                                    return [
                                        TaskStatus::ToDo->value => 'To Do',
                                        TaskStatus::InProgress->value => 'In Progress',
                                        TaskStatus::SentForReview->value => 'Sent for Review',
                                        TaskStatus::Done->value => 'Done',
                                    ];
                                } elseif ($record) {
                                    // Non-admins: Check if task was assigned by an admin or created by them
                                    $isAssignedByAdmin = $record->created_by !== $userId && $record->assigned_to === $userId;
                                    if ($isAssignedByAdmin) {
                                        // Assigned by admin: To Do -> In Progress -> Sent for Review
                                        return [
                                            TaskStatus::ToDo->value => 'To Do',
                                            TaskStatus::InProgress->value => 'In Progress',
                                            TaskStatus::SentForReview->value => 'Sent for Review',
                                        ];
                                    } else {
                                        // Created by user: To Do -> In Progress -> Done
                                        return [
                                            TaskStatus::ToDo->value => 'To Do',
                                            TaskStatus::InProgress->value => 'In Progress',
                                            TaskStatus::Done->value => 'Done',
                                        ];
                                    }
                                }
                                // Default for create (non-admin)
                                return [
                                    TaskStatus::ToDo->value => 'To Do',
                                    TaskStatus::InProgress->value => 'In Progress',
                                    TaskStatus::Done->value => 'Done',
                                ];
                            })
                            ->colors([
                                TaskStatus::ToDo->value => 'info',
                                TaskStatus::InProgress->value => 'primary',
                                TaskStatus::SentForReview->value => 'danger',
                                TaskStatus::Done->value => 'success',
                            ])
                            ->icons([
                                TaskStatus::ToDo->value => 'heroicon-o-list-bullet',
                                TaskStatus::InProgress->value => 'heroicon-o-arrow-path',
                                TaskStatus::SentForReview->value => 'heroicon-o-eye',
                                TaskStatus::Done->value => 'heroicon-o-check-circle',
                            ])
                            ->default(TaskStatus::ToDo->value)
                            ->required()
                            ->disabled(fn () => !$isAdmin && $record && $record->assigned_to !== $userId && $record->created_by !== $userId),
                        Forms\Components\FileUpload::make('proof_url')
                            ->label('Proof Image')
                            ->image()
                            ->directory('proofs')
                            ->visibility('public')
                            ->disk('public')
                            ->helperText('Upload an image as proof (e.g., PNG, JPG).')
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg'])
                            ->maxSize(2048)
                            ->columnSpanFull()
                            ->nullable(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        $isAdmin = Auth::user()->hasRole('admin') || Auth::user()->hasRole('Super Admin');

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-document-text')
                    ->color('gray'),
                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Assigned To')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->icon('heroicon-o-user'),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label('Created By')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->icon('heroicon-o-user-circle'),
                Tables\Columns\TextColumn::make('status')
                    ->color(fn (string $state): string => match ($state) {
                        TaskStatus::ToDo->value => 'info',
                        TaskStatus::InProgress->value => 'primary',
                        TaskStatus::SentForReview->value => 'danger',
                        TaskStatus::Done->value => 'success',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        TaskStatus::ToDo->value => 'heroicon-o-list-bullet',
                        TaskStatus::InProgress->value => 'heroicon-o-arrow-path',
                        TaskStatus::SentForReview->value => 'heroicon-o-eye',
                        TaskStatus::Done->value => 'heroicon-o-check-circle',
                    })
                    ->badge()
                    ->formatStateUsing(fn ($state) => str_replace('_', ' ', ucwords($state)))
                    ->sortable(),
                Tables\Columns\TextColumn::make('proof_url')
                    ->label('Proof')
                    ->formatStateUsing(fn ($state) => $state ? '<a href="' . \Illuminate\Support\Facades\Storage::url($state) . '" target="_blank" class="text-blue-600 hover:underline">View Image</a>' : '-')
                    ->html()
                    ->toggleable()
                    ->icon('heroicon-o-photo'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->icon('heroicon-o-clock'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(TaskStatus::values())
                    ->label('Status'),
                Tables\Filters\Filter::make('assigned_to_me')
                    ->query(fn (Builder $query) => $query->where('assigned_to', Auth::id()))
                    ->label('Assigned to Me')
                    ->visible($isAdmin),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn (Task $record) => $record->created_by == Auth::id() || $record->assigned_to == Auth::id() || $isAdmin)
                    ->icon('heroicon-o-pencil'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible($isAdmin)
                    ->icon('heroicon-o-trash'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if (!($user->hasRole('admin') || $user->hasRole('Super Admin'))) {
            $query->where(function (Builder $q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhere('assigned_to', $user->id);
            });
        }

        return $query;
    }
}