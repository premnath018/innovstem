<?php

namespace App\Http\Controllers;

use App\Services\CourseService;
use App\Helpers\ApiResponse;

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
    public function paginate()
    {
        try {
            $courses = $this->courseService->getPaginatedCourses(9); // Default to 15 items per page
            return ApiResponse::success($courses, 'Paginated courses retrieved successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    /**
     * Get a course by slug.
     */
    public function show($slug)
    {
        $course = $this->courseService->getCourseBySlug($slug);
            return ApiResponse::success($course, 'Course retrieved successfully.');
        // try {
            
        // } catch (\Exception $e) {
        //     return ApiResponse::error($e->getMessage(), 404);
        // }
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
}
