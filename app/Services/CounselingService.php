<?php

namespace App\Services;

use App\Mail\AppointmentStatusMail;
use App\Models\Appointment;
use App\Models\CounselingPackage;
use App\Models\Slot;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class CounselingService
{
    /**
     * Fetch all active counseling packages.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActivePackages()
    {
        return CounselingPackage::where('active', true)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'category', 'package_name', 'price_inr', 'duration', 'includes']);
    }

    /**
     * Fetch available slots for a given date.
     *
     * @param string|null $date
     * @return array
     */
    public function getAvailableSlotsByDate(?string $date)
    {
        $parsedDate = $date ? Carbon::parse($date)->format('Y-m-d') : Carbon::today()->format('Y-m-d');

        // Fetch all active slots for the date
        $slots = Slot::where('slot_date', $parsedDate)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get(['id', 'day_of_week', 'slot_date', 'start_time', 'end_time']);

        if ($slots->isEmpty()) {
            return [
                'message' => 'No slots available for the selected date.',
                'slots' => [],
                'status' => 'no_slots',
            ];
        }

        // Check which slots are booked
        $bookedSlotIds = Appointment::whereIn('slot_id', $slots->pluck('id'))
            ->where('active', true)
            ->pluck('slot_id')
            ->toArray();

        $availableSlots = $slots->filter(fn ($slot) => !in_array($slot->id, $bookedSlotIds));

        if ($availableSlots->isEmpty()) {
            return [
                'message' => 'All slots are booked for the selected date.',
                'slots' => [],
                'status' => 'all_booked',
            ];
        }

        return [
            'message' => 'Available slots retrieved successfully.',
            'slots' => $availableSlots->map(fn ($slot) => [
                'id' => $slot->id,
                'day_of_week' => $slot->day_of_week,
                'slot_date' => $slot->slot_date->format('Y-m-d'),
                'start_time' => Carbon::parse($slot->start_time)->format('h:i A'),
                'end_time' => Carbon::parse($slot->end_time)->format('h:i A'),
            ]),
            'status' => 'available',
        ];
    }

    /**
     * Create a new appointment.
     *
     * @param array $data
     * @return Appointment
     * @throws \Exception
     */
    public function createAppointment(array $data)
    {
        // Validate package and slot
        $package = CounselingPackage::where('id', $data['package_id'])
            ->where('active', true)
            ->firstOrFail();

        $slot = Slot::where('id', $data['slot_id'])
            ->where('is_active', true)
            ->firstOrFail();

        // Check if slot is already booked
        $isBooked = Appointment::where('slot_id', $data['slot_id'])
            ->where('active', true)
            ->exists();

        if ($isBooked) {
            throw new \Exception('Selected slot is already booked.');
        }

        $ackNumber = $this->generateAckNumber();

        // Create appointment
        $appointment = Appointment::create([
            'ack' => $ackNumber,
            'name' => $data['name'],
            'mobile_number' => $data['mobile_number'],
            'email' => $data['email'],
            'class' => $data['class'] ?? null,
            'gender' => $data['gender'] ?? null,
            'ambition' => $data['ambition'] ?? null,
            'user_type' => $data['user_type'],
            'package_id' => $data['package_id'],
            'slot_id' => $data['slot_id'],
            'transaction_id' => $data['transaction_id'] ?? null,
            'amount_paid' => $data['amount_paid'] ?? null,
            'payment_status' => $data['payment_status'] ?? 'Pending',
            'note' => $data['note'] ?? null,
            'active' => true,
        ]);

        if (in_array($appointment->payment_status, ['Paid', 'Failed'])) {
            Mail::to($appointment->email)->queue(new AppointmentStatusMail($appointment, $appointment->payment_status));
        }

        return $appointment;
    }

    /**
     * Fetch appointment details by ID or mobile number.
     *
     * @param int|null $id
     * @param string|null $mobileNumber
     * @return array
     * @throws \Exception
     */
    public function getAppointmentDetails(?string $ack = null, ?string $mobileNumber = null)
    {
        if (!$ack && !$mobileNumber) {
            throw new \Exception('Either appointment ID or mobile number is required.');
        }

        $query = Appointment::query()
            ->with(['package:id,category,package_name', 'slot:id,slot_date,start_time,end_time'])
            ->where('active', true);

        if ($ack) {
            $appointment = $query->where('ack', $ack)->first();

            if (!$appointment) {
                throw new \Exception('Appointment not found.');
            }

            return [
                'type' => 'single',
                'appointment' => $this->formatAppointment($appointment),
            ];
        }

        $appointments = $query->where('mobile_number', $mobileNumber)->get();

        if ($appointments->isEmpty()) {
            throw new \Exception('No appointments found for the provided mobile number.');
        }

        return [
            'type' => 'multiple',
            'appointments' => $appointments->map(fn ($appointment) => $this->formatAppointment($appointment))->toArray(),
        ];
    }

    /**
     * Format appointment data for API response.
     *
     * @param Appointment $appointment
     * @return array
     */
    protected function formatAppointment(Appointment $appointment)
    {
        return [
            'id' => $appointment->id,
            'ack' => $appointment->ack,
            'name' => $appointment->name,
            'mobile_number' => $appointment->mobile_number,
            'email' => $appointment->email,
            'class' => $appointment->class ?? 'N/A',
            'gender' => $appointment->gender ?? 'N/A',
            'ambition' => $appointment->ambition ?? 'N/A',
            'user_type' => $appointment->user_type,
            'package' => [
                'id' => $appointment->package->id,
                'category' => $appointment->package->category,
                'package_name' => $appointment->package->package_name,
            ],
            'slot' => [
                'id' => $appointment->slot->id,
                'slot_date' => $appointment->slot->slot_date->format('Y-m-d'),
                'start_time' => Carbon::parse($appointment->slot->start_time)->format('h:i A'),
                'end_time' => Carbon::parse($appointment->slot->end_time)->format('h:i A'),
            ],
            'transaction_id' => $appointment->transaction_id ?? 'N/A',
            'amount_paid' => $appointment->amount_paid ? number_format($appointment->amount_paid, 2) : 'N/A',
            'appointment_status' => $appointment->appointment_status,
            'payment_status' => $appointment->payment_status,
            'note' => $appointment->note ?? 'N/A',
            'active' => $appointment->active,
            'created_at' => $appointment->created_at->toDateTimeString(),
            'updated_at' => $appointment->updated_at->toDateTimeString(),
        ];
    }


    protected function generateAckNumber(): string
    {
        do {
            $randomNumber = mt_rand(100000, 999999); // Random 6-digit number
            $ackNumber = 'ISA' . $randomNumber; // e.g., ISA123456
        } while (Appointment::where('ack', $ackNumber)->exists());

        return $ackNumber;
    }
}