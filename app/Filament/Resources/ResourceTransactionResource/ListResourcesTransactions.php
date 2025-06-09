<?php

namespace App\Filament\Resources\ResourceTransactionResource\Pages;

use App\Filament\Resources\ResourceTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListResourceTransactions extends ListRecords
{
    protected static string $resource = ResourceTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
