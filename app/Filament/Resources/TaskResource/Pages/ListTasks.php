<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Enums\TaskStatus;
use App\Filament\Resources\TaskResource;
use App\Models\Task;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $isAdmin = Auth::user()->hasRole('admin') || Auth::user()->hasRole('Super Admin');

        $tabs = [
            'all' => Tab::make('All Tasks')
                ->badge(Task::count()),
            TaskStatus::ToDo->value => Tab::make('To Do')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', TaskStatus::ToDo->value))
                ->badge(Task::where('status', TaskStatus::ToDo->value)->count()),
            TaskStatus::InProgress->value => Tab::make('In Progress')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', TaskStatus::InProgress->value))
                ->badge(Task::where('status', TaskStatus::InProgress->value)->count()),
            TaskStatus::Done->value => Tab::make('Done')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', TaskStatus::Done->value))
                ->badge(Task::where('status', TaskStatus::Done->value)->count()),
        ];

        if ($isAdmin) {
            $tabs[TaskStatus::SentForReview->value] = Tab::make('For Review')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', TaskStatus::SentForReview->value))
                ->badge(Task::where('status', TaskStatus::SentForReview->value)->count());
        }

        return $tabs;
    }
}