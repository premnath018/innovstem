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
use Filament\Infolists;
use Filament\Infolists\Infolist;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $navigationLabel = 'Tasks';

    protected static ?string $slug = 'tasks';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        $isAdmin = Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Super Admin');
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
                            ->disabled(fn () => !$isAdmin && $record !== null),
                        Forms\Components\ToggleButtons::make('status')
                            ->inline()
                            ->options(function () use ($isAdmin, $record, $userId) {
                                if ($isAdmin) {
                                    return [
                                        TaskStatus::ToDo->value => 'To Do',
                                        TaskStatus::InProgress->value => 'In Progress',
                                        TaskStatus::SentForReview->value => 'Sent for Review',
                                        TaskStatus::Done->value => 'Done',
                                    ];
                                } elseif ($record) {
                                    $isAssignedByAdmin = $record->created_by !== $userId && $record->assigned_to === $userId;
                                    if ($isAssignedByAdmin) {
                                        return [
                                            TaskStatus::ToDo->value => 'To Do',
                                            TaskStatus::InProgress->value => 'In Progress',
                                            TaskStatus::SentForReview->value => 'Sent for Review',
                                        ];
                                    } else {
                                        return [
                                            TaskStatus::ToDo->value => 'To Do',
                                            TaskStatus::InProgress->value => 'In Progress',
                                            TaskStatus::Done->value => 'Done',
                                        ];
                                    }
                                }
                                return [
                                    TaskStatus::ToDo->value => 'To Do',
                                    TaskStatus::InProgress->value => 'In Progress',
                                    TaskStatus::Done->value => 'Done',
                                ];
                            })
                            ->colors([
                                TaskStatus::ToDo->value => 'info',
                                TaskStatus::InProgress->value => 'primary',
                                TaskStatus::SentForReview->value => 'warning',
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
                        Forms\Components\DatePicker::make('deadline_date')
                            ->label('Deadline Date')
                            ->native(false)
                            ->minDate(now())
                            ->disabled(fn () => !$isAdmin && $record !== null),
                        Forms\Components\Textarea::make('remarks')
                            ->label('Remarks')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\ToggleButtons::make('priority')
                            ->inline()
                            ->options([
                                'low' => 'Low',
                                'medium' => 'Medium',
                                'high' => 'High',
                            ])
                            ->colors([
                                'low' => 'success',
                                'medium' => 'warning',
                                'high' => 'danger',
                            ])
                            ->icons([
                                'low' => 'heroicon-o-arrow-down',
                                'medium' => 'heroicon-o-arrow-right',
                                'high' => 'heroicon-o-arrow-up',
                            ])
                            ->default('medium')
                            ->required()
                            ->disabled(fn () => !$isAdmin && $record !== null),
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
        $isAdmin = Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Super Admin');

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
                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'success',
                        'medium' => 'warning',
                        'high' => 'danger',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'low' => 'heroicon-o-arrow-down',
                        'medium' => 'heroicon-o-arrow-right',
                        'high' => 'heroicon-o-arrow-up',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->sortable()
                    ->toggleable(),
                    Tables\Columns\TextColumn::make('deadline_date')
                    ->label('Deadline')
                    ->date()
                    ->sortable()
                    ->toggleable()
                    ->icon('heroicon-o-calendar'),
                Tables\Columns\TextColumn::make('remarks')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-chat-bubble-left'),
                Tables\Columns\TextColumn::make('proof_url')
                    ->label('Proof')
                    ->formatStateUsing(fn ($state) => $state ? '<a href="' . \Illuminate\Support\Facades\Storage::url($state) . '" target="_blank" class="text-blue-600 hover:underline">View Image</a>' : '-')
                    ->html()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-photo'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->icon('heroicon-o-clock'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        TaskStatus::ToDo->value => 'To Do',
                        TaskStatus::InProgress->value => 'In Progress',
                        TaskStatus::SentForReview->value => 'Sent for Review',
                        TaskStatus::Done->value => 'Done',
                    ])
                    ->native(false)
                    ->label('Status'),
                Tables\Filters\Filter::make('assigned_to_me')
                    ->query(fn (Builder $query) => $query->where('assigned_to', Auth::id()))
                    ->label('Assigned to Me')
                    ->visible($isAdmin),
                Tables\Filters\SelectFilter::make('priority')
                    ->native(false)
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                    ])
                    ->label('Priority'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->color('gray'),
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Task Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('title')
                            ->label('Task Title')
                            ->weight('bold')
                            ->icon('heroicon-o-document-text'),
                        Infolists\Components\TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull()
                            ->icon('heroicon-o-information-circle'),
                        Infolists\Components\Split::make([
                            Infolists\Components\TextEntry::make('status')
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    TaskStatus::ToDo->value => 'info',
                                    TaskStatus::InProgress->value => 'primary',
                                    TaskStatus::SentForReview->value => 'warning',
                                    TaskStatus::Done->value => 'success',
                                })
                                ->icon(fn (string $state): string => match ($state) {
                                    TaskStatus::ToDo->value => 'heroicon-o-list-bullet',
                                    TaskStatus::InProgress->value => 'heroicon-o-arrow-path',
                                    TaskStatus::SentForReview->value => 'heroicon-o-eye',
                                    TaskStatus::Done->value => 'heroicon-o-check-circle',
                                })
                                ->formatStateUsing(fn ($state) => str_replace('_', ' ', ucwords($state))),
                            Infolists\Components\TextEntry::make('priority')
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    'low' => 'success',
                                    'medium' => 'warning',
                                    'high' => 'danger',
                                })
                                ->icon(fn (string $state): string => match ($state) {
                                    'low' => 'heroicon-o-arrow-down',
                                    'medium' => 'heroicon-o-arrow-right',
                                    'high' => 'heroicon-o-arrow-up',
                                })
                                ->formatStateUsing(fn ($state) => ucfirst($state)),
                        ]),
                    ])
                    ->columns(2),
    
                Infolists\Components\Section::make('Assignment Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('assignedTo.name')
                            ->label('Assigned To')
                            ->icon('heroicon-o-user')
                            ->placeholder('Not assigned'),
                        Infolists\Components\TextEntry::make('createdBy.name')
                            ->label('Created By')
                            ->icon('heroicon-o-user-circle'),
                        Infolists\Components\TextEntry::make('deadline_date')
                            ->label('Deadline')
                            ->date()
                            ->icon('heroicon-o-calendar')
                            ->placeholder('No deadline set'),
                    ])
                    ->columns(3),
    
                Infolists\Components\Section::make('Additional Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('remarks')
                            ->label('Remarks')
                            ->columnSpanFull()
                            ->icon('heroicon-o-chat-bubble-left')
                            ->placeholder('No remarks'),
                        Infolists\Components\ImageEntry::make('proof_url')
                            ->label('Proof Image')
                            ->width(300)
                            ->height(200)
                            ->visible(fn ($record) => $record->proof_url !== null)
                            ->columnSpanFull()
                            ->placeholder('No proof image uploaded'),
                    ]),
    
                Infolists\Components\Section::make('Timestamps')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime()
                            ->icon('heroicon-o-clock'),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Last Updated')
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
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
            'view' => Pages\ViewTask::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if (!($user->hasRole('Admin') || $user->hasRole('Super Admin'))) {
            $query->where(function (Builder $q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhere('assigned_to', $user->id);
            });
        }

        return $query;
    }
}