<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Helpers\ApiResponse;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserAuthController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'mobile' => 'required|string|unique:students,mobile',
            'standard' => 'required|string',
            'ambition' => 'nullable|string',
            'parent_no' => 'nullable|string',
            'age' => 'required|integer',
            'gender' => 'required|in:male,female,other',
            'district' => 'required|string',
            'address' => 'required|string',
            'state' => 'required|string',
        ], [
            'email.required' => 'The email field is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email is already registered. Please use a different one.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 6 characters long.',
            'password.confirmed' => 'Passwords do not match.',
            'mobile.required' => 'The mobile number is required.',
            'mobile.unique' => 'This mobile number is already registered.',
            'standard.required' => 'Please provide the standard/class level.',
            'age.required' => 'Please provide the age.',
            'age.integer' => 'Age must be a valid integer.',
            'gender.required' => 'Please select a gender.',
            'gender.in' => 'Invalid gender selection.',
            'district.required' => 'District is required.',
            'address.required' => 'Address is required.',
            'state.required' => 'State is required.',
        ]);
    
        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->first(), 400); // Return only the first error message
        }
    
        try {
            // Create User
            $user = $this->userService->register([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);
    
            // Store Student Details
            $this->userService->createStudentDetails($user->id, $request->all());
    
            $token = JWTAuth::fromUser($user);
    
            return ApiResponse::success(compact('user', 'token'), 'User created successfully', 201);
        } catch (\Exception $e) {
            return ApiResponse::error('An error occurred while registering the user. Please try again.', 500);
        }
    }
    
    
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
    
        // ✅ Check if the email exists
        $user = User::where('email', $credentials['email'])->first();
        if (!$user) {
            return ApiResponse::error('Email not found', 404);
        }
    
        // ✅ Check if the user has a student profile
        if ($user->student) {
            if (!$user->student->active) {
                return ApiResponse::error('Student profile is inactive', 403);
            }
        }
    
        // ✅ Attempt login with JWT
        if (!$token = JWTAuth::attempt($credentials)) {
            return ApiResponse::error('Invalid credentials', 401);
        }
    
        // ✅ Return token with user details
        return ApiResponse::success([
            'token' => $token,
            'user' => [
                'name' => $user->name,
                'email' => $user->email
            ]
        ], 'Login successful');
    }
    
    
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->toJson(), 400);
        }

        $result = $this->userService->generateResetToken($request->email);

        if (!$result) {
            return ApiResponse::error('Email not found', 404);
        }

        // Send email logic here...

        return ApiResponse::success($result, 'Password reset link sent successfully');
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->toJson(), 400);
        }

        $success = $this->userService->resetPassword(
            $request->email,
            $request->token,
            $request->password
        );

        if (!$success) {
            return ApiResponse::error('Invalid or expired token', 400);
        }

        return ApiResponse::success([], 'Password successfully updated');
    }

    public function getUser(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return ApiResponse::error('User not found', 404);
            }

            // Fetch student details
            $student = Student::where('user_id', $user->id)->first();
            unset($student->id);
            unset($student->user_id);
            return ApiResponse::success([
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                
                'student' => $student
            ], 'User retrieved successfully.');
        } catch (JWTException $e) {
            return ApiResponse::error('Invalid or expired token', 401);
        }
    }

    /**
     * Update user and student details.
     */
    public function updateUser(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return ApiResponse::error('User not found', 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string|max:255',
                'mobile' => 'nullable|string|unique:students,mobile,' . $user->id . ',user_id',
                'standard' => 'nullable|string',
                'ambition' => 'nullable|string',
                'parent_no' => 'nullable|string',
                'age' => 'nullable|integer',
                'gender' => 'nullable|in:male,female,other',
                'district' => 'nullable|string',
                'address' => 'nullable|string',
                'state' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return ApiResponse::error($validator->errors()->first(), 400);
            }

            // Update user details if provided
            $user->update([
                'name' => $request->name ?? $user->name,
            ]);

            // Update or create student details
            $student = Student::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $request->name ?? $user->name,
                    'mobile' => $request->mobile ?? ($user->student->mobile ?? null),
                    'standard' => $request->standard ?? ($user->student->standard ?? null),
                    'ambition' => $request->ambition ?? ($user->student->ambition ?? null),
                    'parent_no' => $request->parent_no ?? ($user->student->parent_no ?? null),
                    'age' => $request->age ?? ($user->student->age ?? null),
                    'gender' => $request->gender ?? ($user->student->gender ?? null),
                    'district' => $request->district ?? ($user->student->district ?? null),
                    'address' => $request->address ?? ($user->student->address ?? null),
                    'state' => $request->state ?? ($user->student->state ?? null),
                ]
            );

            return ApiResponse::success('User details updated successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error('An error occurred while updating user details.', 500);
        }
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return ApiResponse::success([], 'Successfully logged out');
        } catch (JWTException $e) {
            return ApiResponse::error('Failed to logout, please try again later', 500);
        }
    }

    // Refresh token
    public function refresh()
    {
        try {
            $newToken = JWTAuth::refresh();
            return ApiResponse::success(['access_token' => $newToken], 'Token refreshed');
        } catch (JWTException $e) {
            return ApiResponse::error('Failed to refresh token', 500);
        }
    }
}
