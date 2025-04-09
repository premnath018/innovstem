<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Mail\ApplicationReceived;
use App\Mail\TaskAssigned;
use App\Models\Career;
use App\Models\CareerApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CareerController extends Controller
{
    /**
     * List all careers.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $careers = Career::all()->where('is_active',true);

        return ApiResponse::success(
            data: $careers,
            message: 'Careers retrieved successfully'
        );
    }

    /**
     * Get details of a specific career.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $career = Career::find($id);

        if (!$career) {
            return ApiResponse::error(
                message: 'Career not found',
                status: 404
            );
        }

        return ApiResponse::success(
            data: $career,
            message: 'Career details retrieved successfully'
        );
    }

    /**
     * Submit an application for a career.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function apply(Request $request, $id)
    {
        $career = Career::find($id);

        if (!$career) {
            return ApiResponse::error(
                message: 'Career not found',
                status: 404
            );
        }

        if (!$career->is_active) {
            return ApiResponse::error(
                message: 'This career opportunity is no longer active',
                status: 400
            );
        }

        // Validation rules
        $validator = Validator::make($request->all(), [
            'applicant_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'cover_letter' => 'nullable|string|max:65535',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return ApiResponse::error(
                message: 'Validation failed',
                status: 422,
                data: $validator->errors()
            );
        }

        // Handle resume upload
        $resumePath = $request->file('resume')->store('resumes', 'public');

        // Create application
        $application = CareerApplication::create([
            'career_id' => $career->id,
            'applicant_name' => $request->applicant_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'cover_letter' => $request->cover_letter,
            'resume_path' => $resumePath,
            'status' => 'Pending',
        ]);

        Mail::to($application->email)->queue(new ApplicationReceived($application, $career));

        // Increment registration count
        $career->increment('registration_count');

        return ApiResponse::success(
            data: $application,
            message: 'Application submitted successfully',
            status: 201
        );
    }

    /**
     * Get details of a specific application.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getApplication($id)
    {
        $application = CareerApplication::find($id);

        if (!$application) {
            return ApiResponse::error(
                message: 'Application not found',
                status: 404
            );
        }

        // Add resume URL to response
        $application->resume_url = $application->resume_path ? Storage::url($application->resume_path) : null;

        return ApiResponse::success(
            data: $application,
            message: 'Application details retrieved successfully'
        );
    }
}