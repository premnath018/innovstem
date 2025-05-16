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
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\CounselingController;




/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
|
| Routes for user authentication including registration, login, password 
| reset, and fetching user details.
|
*/

// User registration
Route::post('register', [UserAuthController::class, 'register']);

// User login
Route::post('login', [UserAuthController::class, 'login']);

// Forgot password request
Route::post('forgot-password', [UserAuthController::class, 'forgotPassword']);

// Reset password
Route::post('reset-password', [UserAuthController::class, 'resetPassword'])->name('password.reset');

/*
|--------------------------------------------------------------------------
| Protected Routes (Requires Authentication)
|--------------------------------------------------------------------------
|
| These routes are accessible only to authenticated users with a valid JWT.
| The JwtMiddleware ensures security and token validation.
|
*/

Route::middleware([JwtMiddleware::class])->group(function () {
    // User logout
    Route::post('logout', [UserAuthController::class, 'logout']);

    // Refresh authentication token
    Route::get('refresh', [UserAuthController::class, 'refresh']);

    // Get authenticated user details
    Route::get('user', [UserAuthController::class, 'getUser']);

    // Get authenticated user details
    Route::post('user', [UserAuthController::class, 'updateUser']);


    /*
    |--------------------------------------------------------------------------
    | Student Routes
    |--------------------------------------------------------------------------
    |
    | Routes for student-related actions such as enrolling in courses, 
    | attending webinars, and fetching enrolled courses.
    |
    */
    
    Route::get('/student/courses', [StudentController::class, 'enrolledCourses']);
    Route::get('/student/webinars', [StudentController::class, 'attendedWebinars']);
    Route::post('/student/enroll-course', [StudentController::class, 'enrollCourse']);
    Route::post('/student/attend-webinar', [StudentController::class, 'attendWebinar']);

    /*
    |--------------------------------------------------------------------------
    | Quiz Routes
    |--------------------------------------------------------------------------
    |
    | Routes for handling quizzes, including fetching quiz questions and 
    | submitting answers.
    |
    */
    
    Route::prefix('quiz')->group(function () {
        // Fetch quiz questions based on type and slug
        Route::get('/{quiz_id}', [QuizController::class, 'show'])->name('quiz.show');

        // Submit quiz answers
        Route::post('/{quiz_id}/submit', [QuizController::class, 'submit'])->name('quiz.submit');
    });
});

/*
|--------------------------------------------------------------------------
| Blog Routes
|--------------------------------------------------------------------------
|
| Routes for managing and retrieving blog content, including search and 
| pagination.
|
*/

Route::prefix('blogs')->group(function () {
    Route::get('/', [BlogController::class, 'paginate'])->name('blogs.paginate'); // Fetch paginated blogs
    Route::get('/d/{slug}', [BlogController::class, 'show']); // Fetch a blog by slug
    Route::get('/search', [BlogController::class, 'search'])->name('blogs.search'); // Search blogs
    Route::get('/recent', [BlogController::class, 'recent'])->name('blogs.recent'); // Fetch recent blogs
});

/*
|--------------------------------------------------------------------------
| Course Routes
|--------------------------------------------------------------------------
|
| Routes for managing courses, including categories, search, and details.
|
*/

Route::prefix('courses')->group(function () {
    Route::get('/', [CourseController::class, 'paginate'])->name('courses.paginate'); // Fetch paginated courses
    Route::get('/categories', [CourseController::class, 'category'])->name('courses.categories'); // Fetch course categories
    Route::get('/category/{category}', [CourseController::class, 'showCategory'])->name('courses.category'); // Fetch courses by category
    Route::get('/search', [CourseController::class, 'search'])->name('courses.search'); // Search courses
    Route::get('/recent', [CourseController::class, 'recent'])->name('courses.recent'); // Fetch recent courses
    Route::get('/d/{slug}', [CourseController::class, 'show'])->name('courses.show'); // Fetch a course by slug
});

/*
|--------------------------------------------------------------------------
| Resource Routes
|--------------------------------------------------------------------------
|
| Routes for retrieving resources, including search, pagination, and details.
|
*/

Route::prefix('resources')->group(function () {
    Route::get('/', [ResourceController::class, 'paginate'])->name('resources.paginate'); // Fetch paginated resources
    Route::get('/d/{slug}', [ResourceController::class, 'show'])->name('resources.show'); // Fetch a resource by slug
    Route::get('/search', [ResourceController::class, 'search'])->name('resources.search'); // Search resources
    Route::get('/recent', [ResourceController::class, 'recent'])->name('resources.recent'); // Fetch recent resources
});

/*
|--------------------------------------------------------------------------
| Webinar Routes
|--------------------------------------------------------------------------
|
| Routes for retrieving webinar details, including pagination and search.
|
*/

Route::prefix('webinars')->group(function () {
    Route::get('/', [WebinarController::class, 'paginate'])->name('webinars.paginate'); // Fetch paginated webinars
    Route::get('/d/{slug}', [WebinarController::class, 'show'])->name('webinars.show'); // Fetch a webinar by slug
    Route::get('/search', [WebinarController::class, 'search'])->name('webinars.search'); // Search webinars
    Route::get('/recent', [WebinarController::class, 'recent'])->name('webinars.recent'); // Fetch recent webinars
});

/*
|--------------------------------------------------------------------------
| Home & Recommendation Routes
|--------------------------------------------------------------------------
|
| Routes for fetching homepage data and recommended content.
|
*/

// Fetch top 5 recent blogs, courses, and webinars
Route::get('/home', [HomeController::class, 'home'])->name('home');

Route::get('/news', [HomeController::class, 'news'])->name('news');

Route::post('/t' , [HomeController::class , 'track']);

Route::get('/v' , [HomeController::class , 'getTotalViews']);

// Fetch recommended items based on user preferences
Route::get('/recommend', [HomeController::class, 'recommend'])->name('recommend');


/*
|--------------------------------------------------------------------------
| Career Routes
|--------------------------------------------------------------------------
|
| Routes for retrieving Career details, application detials and Apply jobs.
|
*/

Route::get('/careers', [CareerController::class, 'index']);
Route::get('/careers/{id}', [CareerController::class, 'show']);
Route::post('/careers/{id}', [CareerController::class, 'apply']);
Route::get('/careers/application/{id}', [CareerController::class, 'getApplication']);



Route::get('/packages', [CounselingController::class, 'packages']);
Route::get('/slots', [CounselingController::class, 'slots']);
Route::post('/appointments', [CounselingController::class, 'createAppointment']);
Route::get('/appointments', [CounselingController::class, 'appointmentDetails']);