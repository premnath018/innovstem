<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserAuthController;
use App\Http\Middleware\JwtMiddleware;

Route::post('register',[UserAuthController::class,'register']);
Route::post('login',[UserAuthController::class,'login']);
Route::post('forgot-password', [UserAuthController::class, 'forgotPassword']);
Route::post('reset-password', [UserAuthController::class, 'resetPassword'])->name('password.reset');



Route::middleware([JwtMiddleware::class])->group(function () {
    Route::get('user', [UserAuthController::class, 'getUser']);
    Route::post('logout', [UserAuthController::class, 'logout']);
});