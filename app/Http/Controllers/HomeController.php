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

    /**
     * Increment view count for a specific blog, course, or webinar.
     */
    public function view(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|string|in:blog,course,webinar',
                'id' => 'required|integer',
            ]);

            $type = $request->input('type');
            $id = $request->input('id');

            switch ($type) {
                case 'blog':
                    $entity = $this->blogService->getBlogById($id);
                    break;
                case 'course':
                    $entity = $this->courseService->getCourseById($id);
                    break;
                case 'webinar':
                    $entity = $this->webinarService->getWebinarById($id);
                    break;
                default:
                    return ApiResponse::error('Invalid type provided.', 400);
            }

            if (!$entity) {
                return ApiResponse::error(ucfirst($type) . ' not found.', 404);
            }

            // Increment the view count and save
            $entity->increment('view_count');

            return ApiResponse::success([
                'id' => $entity->id,
                'type' => $type,
                'view_count' => $entity->view_count,
            ], ucfirst($type) . ' view count updated successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }
}
