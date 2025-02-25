<?php

namespace App\Filament\Resources\WebinarAttendanceResource\Pages;

use App\Filament\Resources\WebinarAttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWebinarAttendance extends EditRecord
{
    protected static string $resource = WebinarAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
