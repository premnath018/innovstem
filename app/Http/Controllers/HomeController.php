<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use App\Services\BlogService;
use App\Services\CourseService;
use App\Services\WebinarService;

class HomeController extends Controller
{
    protected $blogService;
    protected $courseService;
    protected $webinarService;

    public function __construct(BlogService $blogService, CourseService $courseService, WebinarService $webinarService)
    {
        $this->blogService = $blogService;
        $this->courseService = $courseService;
        $this->webinarService = $webinarService;
    }

    /**
     * Get top 5 recent blogs, courses, and webinars.
     */
    public function home()
    {
        try {
            $blogs = $this->blogService->getRecentBlogs(5);
            $courses = $this->courseService->getRecentCourses(5);
            $webinars = $this->webinarService->getRecentWebinars(5);

            return ApiResponse::success([
                'blogs' => $blogs,
                'courses' => $courses,
                'webinars' => $webinars,
            ], 'Recent content retrieved successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

   
}
