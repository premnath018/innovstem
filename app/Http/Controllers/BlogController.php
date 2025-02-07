<?php

namespace App\Http\Controllers;

use App\Services\BlogService;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    protected $blogService;

    public function __construct(BlogService $blogService)
    {
        $this->blogService = $blogService;
    }

    /**
     * Get all blogs.
     */
    public function index()
    {
        try {
            $blogs = $this->blogService->getAllBlogs();
            return ApiResponse::success($blogs, 'Blogs retrieved successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    /**
     * Get a blog by slug.
     */
    public function show($slug)
    {
        try {
            $blog = $this->blogService->getBlogBySlug($slug);
            return ApiResponse::success($blog, 'Blog retrieved successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 404);
        }
    }


    public function paginate()
    {
        try {
            $blogs = $this->blogService->getPaginatedBlogs(9); // Default to 15 items per page
            return ApiResponse::success($blogs, 'Paginated blogs retrieved successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    public function recent()
    {
        try {
            $courses = $this->blogService->getRecentBlogs(5); // Default to 5 recent courses
            return ApiResponse::success($courses, 'Recent blogs retrieved successfully.');
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

            $blogs = $this->blogService->searchBlogs($keyword, $perPage);

            return ApiResponse::success($blogs, 'Search results retrieved successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }
}
