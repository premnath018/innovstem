<?php

namespace App\Http\Controllers;

use App\Services\CourseService;
use App\Helpers\ApiResponse;
use App\Models\Student;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class CourseController extends Controller
{
    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    /**
     * Get paginated courses.
     */
    public function paginate(Request $request)
    {
        try {
            $categorySlug = $request->query('category'); // Get category from query params
            $perPage = $request->query('perPage', 9); 
    
            $courses = $this->courseService->getPaginatedCourses($categorySlug, $perPage);
    
            return ApiResponse::success($courses, 'Courses retrieved successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }
    
    /**
     * Get a course by slug.
     */
    public function show(Request $request, $slug)
    {
        try {
            $user = null;
            $studentId = null;

            // Check if the request has an Authorization token
            if ($request->hasHeader('Authorization')) {
                try {
                    $user = JWTAuth::parseToken()->authenticate();
                 //   dd($user);
                    if ($user) {
                        $student = Student::where('user_id', $user->id)->first();
                        $studentId = $student ? $student->id : null;
                    }
                } catch (\Exception $e) {
                    // Token is invalid or not provided, continue without user
                }
            }

            $course = $this->courseService->getCourseBySlug($slug, $studentId);

            return ApiResponse::success($course, 'Course retrieved successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 404);
        }
    }



    /**
     * Get recent courses.
     */
    public function recent()
    {
        try {
            $courses = $this->courseService->getRecentCourses(5); // Default to 5 recent courses
            return ApiResponse::success($courses, 'Recent courses retrieved successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $request->validate([
                'keyword' => 'required|string|min:2',
                'perPage' => 'nullable|integer|min:1'
            ]);

            $keyword = $request->input('keyword');
            $perPage = $request->input('perPage', 9);

            $courses = $this->courseService->searchcourses($keyword, $perPage);

            return ApiResponse::success($courses, 'Search results retrieved successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    public function category(){
        try {
            $categroies = $this->courseService->allCategroies();

            return ApiResponse::success($categroies, 'Categories retrieved successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }

    }

    public function showCategory(Request $request, $categorySlug)
    {
        try {
            $perPage = $request->input('perPage', 100);
            $courses = $this->courseService->getCoursesByCategory($categorySlug, $perPage);
            return ApiResponse::success($courses, 'Courses retrieved successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }
}
