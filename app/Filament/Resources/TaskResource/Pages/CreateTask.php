<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use App\Mail\TaskAssigned;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;

    protected function afterCreate(): void
    {
        $task = $this->record;
        if ($task->assigned_to &&(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Super Admin'))) {
            $assignedUser = \App\Models\User::find($task->assigned_to);
            if ($assignedUser) {
                Mail::to($assignedUser)->queue(new TaskAssigned($assignedUser, $task));
            }
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::id();
        return $data;
    }

        
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}   