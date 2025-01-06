<?php

namespace App\Filament\Resources\WebinarResource\Pages;

use App\Filament\Resources\WebinarResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateWebinar extends CreateRecord
{
    protected static string $resource = WebinarResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['sync_meta_title'], $data['sync_slug'], $data['sync_meta_description']);
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
}
