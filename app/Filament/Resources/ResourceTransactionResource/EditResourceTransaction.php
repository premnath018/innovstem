<?php

namespace App\Filament\Resources\ResourceTransactionResource\Pages;

use App\Filament\Resources\ResourceTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditResourceTransaction extends EditRecord
{
    protected static string $resource = ResourceTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
}
