<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Resource;
use App\Models\ResourceTransaction;
use App\Services\CounselingService;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class RazorpayController extends Controller
{
    protected $razorpay;
    protected $counselingService;

    public function __construct(CounselingService $counselingService)
    {
        $this->razorpay = new Api(config('services.razorpay.key_id'), config('services.razorpay.key_secret'));
        $this->counselingService = $counselingService;
    }

    /**
     * Create a Razorpay order.
     */
    public function createOrder(Request $request)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:100', // Amount in paise
                'currency' => 'required|in:INR',
                'package_id' => 'required|exists:counseling_packages,id',
            ]);

            $options = [
                'amount' => $request->amount, // Already in paise from frontend
                'currency' => $request->currency,
                'receipt' => 'receipt_' . $request->package_id . '_' . time(),
                'payment_capture' => 1, // Auto-capture payment
            ];

            $order = $this->razorpay->order->create($options);

            return ApiResponse::success([
                'order_id' => $order->id,
                'currency' => $order->currency,
                'amount' => $order->amount,
            ], 'Order created successfully.');
        } catch (\Exception $e) {
            Log::error('Razorpay create order error: ' . $e->getMessage());
            return ApiResponse::error('Failed to create order: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Verify payment and create appointment.
     */
    public function verifyPayment(Request $request)
    {
        try {
            $request->validate([
                'razorpay_order_id' => 'required|string',
                'razorpay_payment_id' => 'required|string',
                'razorpay_signature' => 'required|string',
                'appointment_data' => 'required|array',
                'appointment_data.name' => 'required|string|max:100',
                'appointment_data.mobile_number' => 'required|string|max:15',
                'appointment_data.email' => 'required|email|max:100',
                'appointment_data.class' => 'nullable|string|max:20',
                'appointment_data.gender' => 'nullable|in:Male,Female,Other',
                'appointment_data.ambition' => 'nullable|string|max:255',
                'appointment_data.user_type' => 'required|in:Student,Parent,Teacher',
                'appointment_data.package_id' => 'required|exists:counseling_packages,id',
                'appointment_data.slot_id' => 'required|exists:slots,id',
            ]);

            // Verify signature
            $attributes = $request->razorpay_order_id . '|' . $request->razorpay_payment_id;
            $generatedSignature = hash_hmac('sha256', $attributes, config('services.razorpay.key_secret'));

            if ($generatedSignature !== $request->razorpay_signature) {
                return ApiResponse::error('Invalid payment signature.', 400);
            }

            // Create appointment
            $appointmentData = $request->appointment_data;
            $appointmentData['transaction_id'] = $request->razorpay_payment_id;
            $appointmentData['amount_paid'] = $request->appointment_data['amount'] / 100; // Convert paise to INR
            $appointmentData['payment_status'] = 'Paid';

            $appointment = $this->counselingService->createAppointment($appointmentData);

            return ApiResponse::success($appointment, 'Payment verified and appointment created successfully.');
        } catch (\Exception $e) {
            Log::error('Razorpay verify payment error: ' . $e->getMessage());
            return ApiResponse::error('Failed to verify payment or create appointment: ' . $e->getMessage(), 500);
        }
    }

public function createResourceOrder(Request $request)
    {
        try {
            $request->validate([
                'resource_id' => 'required|exists:resources,id',
            ]);

            $resource = Resource::findOrFail($request->resource_id);

            if ($resource->type !== 'paid') {
                return ApiResponse::error('This resource is not a paid resource.', 400);
            }

            $user = JWTAuth::parseToken()->authenticate();

            // Check if user already has access
            $existingTransaction = ResourceTransaction::where('user_id', $user->id)
                ->where('resource_id', $resource->id)
                ->where('status', 'paid')
                ->exists();

            if ($existingTransaction) {
                return ApiResponse::error('You already have access to this resource.', 400);
            }

            $options = [
                'amount' => $resource->amount * 100, // Convert INR to paise
                'currency' => 'INR',
                'receipt' => 'resource_' . $resource->id . '_' . time(),
                'payment_capture' => 1, // Auto-capture payment
            ];

            $order = $this->razorpay->order->create($options);

            // Create a pending transaction
            $transaction = ResourceTransaction::create([
                'user_id' => $user->id,
                'resource_id' => $resource->id,
                'razorpay_order_id' => $order->id,
                'razorpay_payment_id' => null,
                'razorpay_signature' => null,
                'amount' => $resource->amount,
                'currency' => 'INR',
                'status' => 'pending',
            ]);

            return ApiResponse::success([
                'order_id' => $order->id,
                'currency' => $order->currency,
                'amount' => $order->amount,
                'transaction_id' => $transaction->id,
            ], 'Order created successfully.');
        } catch (\Exception $e) {
            Log::error('Razorpay create resource order error: ' . $e->getMessage());
            return ApiResponse::error('Failed to create order: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Verify payment for a resource.
     */
    public function verifyResourcePayment(Request $request)
    {
        try {
            $request->validate([
                'razorpay_order_id' => 'required|string',
                'razorpay_payment_id' => 'required|string',
                'razorpay_signature' => 'required|string',
                'transaction_id' => 'required|exists:resource_transactions,id',
            ]);

            // Verify signature
            $attributes = $request->razorpay_order_id . '|' . $request->razorpay_payment_id;
            $generatedSignature = hash_hmac('sha256', $attributes, config('services.razorpay.key_secret'));

            if ($generatedSignature !== $request->razorpay_signature) {
                return ApiResponse::error('Invalid payment signature.', 400);
            }

            // Update transaction
            $transaction = ResourceTransaction::findOrFail($request->transaction_id);
            $transaction->update([
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
                'status' => 'paid',
            ]);

            return ApiResponse::success($transaction, 'Payment verified successfully.');
        } catch (\Exception $e) {
            Log::error('Razorpay verify resource payment error: ' . $e->getMessage());
            return ApiResponse::error('Failed to verify payment: ' . $e->getMessage(), 500);
        }
    }


}