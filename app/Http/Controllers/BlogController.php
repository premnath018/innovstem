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
}
