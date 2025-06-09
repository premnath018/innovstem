<?php

namespace App\Filament\Resources\ResourceTransactionResource\Pages;

use App\Filament\Resources\ResourceTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateResourceTransaction extends CreateRecord
{
    protected static string $resource = ResourceTransactionResource::class;

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
