<?php

namespace App\Filament\Resources\AppointmentResource\Pages;

use App\Filament\Resources\AppointmentResource;
use App\Models\Appointment;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAppointment extends CreateRecord
{
    protected static string $resource = AppointmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Generate a unique ack_number
        $data['ack'] = $this->generateAckNumber();

        // Return the modified data for appointment creation
        return $data;
    }

    /**
     * Generate a unique ISA acknowledgment number.
     *
     * @return string
     */
    protected function generateAckNumber(): string
    {
        do {
            $randomNumber = mt_rand(100000, 999999); // Random 6-digit number
            $ackNumber = 'ISA' . $randomNumber; // e.g., ISA123456
        } while (Appointment::where('ack', $ackNumber)->exists());

        return $ackNumber;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}