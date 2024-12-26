<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Helpers\ApiResponse;
use App\Mail\ResetPasswordMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class UserAuthController extends Controller
{
    // User registration
    public function register(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->toJson(), 400);
        }

        // Check if user already exists
        if (User::where('email', $request->email)->exists()) {
            return ApiResponse::error('User already exists with this email.', 409);
        }

        // Create new user
        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        // Generate JWT token
        $token = JWTAuth::fromUser($user);

        return ApiResponse::success(compact('user', 'token'), 'User created successfully', 201);
    }

    // User login
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return ApiResponse::error('Invalid credentials', 401);
            }

            // Get authenticated user
            $user = auth()->user();

            // Attach the role to the token (optional)
            $token = JWTAuth::claims(['email' => $user->email])->fromUser($user);

            return ApiResponse::success(compact('token'), 'Login successful');
        } catch (JWTException $e) {
            return ApiResponse::error('Could not create token', 500);
        }
    }

    // Get authenticated user
    public function getUser()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return ApiResponse::error('User not found', 404);
            }
        } catch (JWTException $e) {
            return ApiResponse::error('Invalid token', 400);
        }

        return ApiResponse::success(compact('user'), 'Authenticated user details');
    }

    // User logout
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

    //forgot password 
    public function forgotPassword(Request $request)
    {
        // Validate email input
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
        ]);
    
        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->toJson(), 400);
        }
    
        // Check if the email exists
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return ApiResponse::error('Email not found', 404);
        }
    
        // Generate reset token
        $token = Str::random(60);
    
        // Store the token and its expiration (e.g., 1 hour expiry)
        $user->reset_token = $token;
        $user->reset_token_expiry = Carbon::now()->addHours(1); // Token expires in 1 hour
        $user->save();
    
        // Send custom reset password email
        Mail::to($user->email)->send(new ResetPasswordMail($token, $user->email));
    
        return ApiResponse::success([], 'Password reset link sent successfully');
    }

    //reset password
    public function resetPassword(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->toJson(), 400);
        }

        // Find the user by email
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return ApiResponse::error('Email not found', 404);
        }

        // Check if the token matches and is not expired
        if ($user->reset_token !== $request->token) {
            return ApiResponse::error('Invalid or expired token', 400);
        }

        if (Carbon::now()->greaterThan($user->reset_token_expiry)) {
            return ApiResponse::error('Token has expired', 400);
        }

        // Update the user's password
        $user->password = Hash::make($request->password);
        $user->reset_token = null; // Clear the token after password reset
        $user->reset_token_expiry = null; // Clear the token expiry
        $user->save();

        return ApiResponse::success([], 'Password successfully updated');
    }

}
