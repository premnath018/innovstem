<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserAuthController;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\WebinarController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\QuizController;
use App\Models\Webinar;

Route::post('register',[UserAuthController::class,'register']);
Route::post('login',[UserAuthController::class,'login']);
Route::post('forgot-password', [UserAuthController::class, 'forgotPassword']);
Route::post('reset-password', [UserAuthController::class, 'resetPassword'])->name('password.reset');
Route::get('user', [UserAuthController::class, 'getUser']);



Route::middleware([JwtMiddleware::class])->group(function () {
    Route::post('logout', [UserAuthController::class, 'logout']);
    Route::get('refresh',[UserAuthController::class, 'refresh']);
});

Route::prefix('blogs')->group(function () {
    Route::get('/', [BlogController::class, 'paginate'])->name('blogs.paginate'); // Paginated blogs
    Route::get('/d/{slug}', [BlogController::class, 'show']); // Blog by slug
    Route::get('/search', [BlogController::class, 'search'])->name('blogs.search');
    Route::get('/recent', [BlogController::class, 'recent'])->name('blogs.recent'); // Recent blogs
});
Route::prefix('courses')->group(function () {
    Route::get('/', [CourseController::class, 'paginate'])->name('courses.paginate'); // Paginated courses
    Route::get('/categories', [CourseController::class,'category'])->name('courses.categories');
    Route::get('/category/{category}', [CourseController::class,'showCategory'])->name('courses.category');
    Route::get('/search', [CourseController::class, 'search'])->name('courses.search');
    Route::get('/recent', [CourseController::class, 'recent'])->name('courses.recent'); // Recent courses
    Route::get('/d/{slug}', [CourseController::class, 'show'])->name('courses.show'); // Course by slug
});

Route::prefix('resources')->group(function () {
    Route::get('/', [ResourceController::class, 'paginate'])->name('resources.paginate'); // Paginated resources
    Route::get('/d/{slug}', [ResourceController::class, 'show'])->name('resources.show'); // Course by slug
    Route::get('/search', [ResourceController::class, 'search'])->name('resources.search');
    Route::get('/recent', [ResourceController::class, 'recent'])->name('resources.recent'); // Recent resources
});

Route::prefix('webinars')->group(function () {
    Route::get('/', [WebinarController::class, 'paginate'])->name('webinars.paginate'); // Paginated webinars
    Route::get('/d/{slug}', [WebinarController::class, 'show'])->name('webinars.show'); // Course by slug
    Route::get('/search', [WebinarController::class, 'search'])->name('webinars.search');
    Route::get('/recent', [WebinarController::class, 'recent'])->name('webinars.recent'); // Recent webinars
});

Route::prefix('quiz')->group(function () {
    Route::get('/{type}/{slug}', [QuizController::class, 'show'])->name('quiz.show'); // List questions & options
    Route::post('/{type}/{slug}/submit', [QuizController::class, 'submit'])->name('quiz.submit'); // Submit answers
});


Route::get('/home', [HomeController::class, 'home'])->name('home'); // Fetch top 5 recent items
Route::get('/recommend', [HomeController::class, 'recommend'])->name('recommend'); // Fetch top 5 recent items

