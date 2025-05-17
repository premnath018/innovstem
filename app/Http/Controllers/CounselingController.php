<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use App\Services\CounselingService;

class CounselingController extends Controller
{
    protected $counselingService;

    public function __construct(
        CounselingService $counselingService
    ) {
        $this->counselingService = $counselingService;
    }

    /**
     * Fetch all active counseling packages.
     */
    public function packages()
    {
        try {
            $packages = $this->counselingService->getActivePackages();

            return ApiResponse::success($packages, 'Active packages retrieved successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    /**
     * Fetch available slots for a given date.
     */
    public function slots(Request $request)
    {
        try {
            $date = $request->input('date'); // Optional date parameter
            $slots = $this->counselingService->getAvailableSlotsByDate($date);

            return ApiResponse::success($slots['slots'], $slots['message']);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    /**
     * Create a new appointment.
     */
    public function createAppointment(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:100',
                'mobile_number' => 'required|string|max:15',
                'email' => 'required|email|max:100',
                'class' => 'nullable|string|max:20',
                'gender' => 'nullable|in:Male,Female,Other',
                'ambition' => 'nullable|string|max:255',
                'user_type' => 'required|in:Student,Parent,Teacher',
                'package_id' => 'required|exists:counseling_packages,id',
                'slot_id' => 'required|exists:slots,id',
                'transaction_id' => 'nullable|string|max:100',
                'amount_paid' => 'nullable|numeric|min:0',
                'payment_status' => 'nullable|in:Pending,Paid,Failed',
                'note' => 'nullable|string',
            ]);

            $appointment = $this->counselingService->createAppointment($request->all());

            return ApiResponse::success($appointment, 'Appointment created successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    /**
     * Fetch appointment details by ID or mobile number.
     */
    public function appointmentDetails(Request $request)
    {
        try {
            $request->validate([
                'ack' => 'nullable|exists:appointments,ack',
                'mobile_number' => 'nullable|string|max:15',
            ]);

            if (!$request->has('ack') && !$request->has('mobile_number')) {
                return ApiResponse::error('Either appointment ID or mobile number is required.', 422);
            }

            $details = $this->counselingService->getAppointmentDetails(
                $request->input('ack'),
                $request->input('mobile_number')
            );

            return ApiResponse::success($details, 'Appointment details retrieved successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }
}