<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Automatically set the created_by field to the current user's ID
        $data['created_by'] = Auth::id();
        return $data;
    }

        
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}   