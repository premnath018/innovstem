<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
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
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->toJson(), 400);
        }

        $user = $this->userService->register($request->all());
        $token = JWTAuth::fromUser($user);

        return ApiResponse::success(compact('user', 'token'), 'User created successfully', 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return ApiResponse::error('Invalid credentials', 401);
        }

        return ApiResponse::success(compact('token'), 'Login successful');
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

        return ApiResponse::success([], 'Password reset link sent successfully');
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

    public function getUser(Request $request){
        $user = $this->userService->getUser($request->email);
        if (!$user) {
            return ApiResponse::error('User not found', 404);
        }
        return ApiResponse::success(compact('user'));

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
