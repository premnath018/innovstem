<?php

namespace App\Services;

use App\Repositories\BlogRepository;

class BlogService
{
    protected $blogRepository;

    public function __construct(BlogRepository $blogRepository)
    {
        $this->blogRepository = $blogRepository;
    }

    /**
     * Find a blog by slug.
     */
    public function getBlogBySlug(string $slug)
    {
        $blog = $this->blogRepository->findBySlug($slug);
        $blog->category_name = $blog->category->name;
        unset($blog->category);

        if (!$blog) {
            throw new \Exception('Blog not found');
        }

        return $blog;
    }

    /**
     * Find a blog by ID.
     */
    public function getBlogById(int $id)
    {
        $blog = $this->blogRepository->findById($id);

        if (!$blog) {
            throw new \Exception('Blog not found');
        }

        return $blog;
    }

    /**
     * Get all blogs with limited fields.
     */
    public function getAllBlogs()
    {
        return $this->blogRepository->getAll()->map(function ($blog) {
            return $this->transformBlog($blog);
        });
    }

    /**
     * Get paginated blogs with limited fields.
     */
    public function getPaginatedBlogs(int $perPage = 15)
    {
        $paginatedBlogs = $this->blogRepository->paginate($perPage);

        // Transform the collection inside the paginator
        $transformedBlogs = $paginatedBlogs->getCollection()->map(function ($blog) {
            return $this->transformBlog($blog);
        });

        // Replace the paginator's collection with the transformed data
        $paginatedBlogs->setCollection($transformedBlogs);

        return $paginatedBlogs;
    }

    /**
     * Get recent blogs with limited fields.
     */
    public function getRecentBlogs(int $limit = 5)
    {
        return $this->blogRepository->getRecent($limit)->map(function ($blog) {
            return $this->transformBlog($blog);
        });
    }

    /**
     * Transform a blog to include only the required fields.
     */
    protected function transformBlog($blog)
    {
        return [
            'id' => $blog->id,
            'slug' => $blog->blog_slug,
            'title' => $blog->title,
            'description' => $blog->blog_description,
            'thumbnail' => $blog->blog_thumbnail,
            'category_name' => $blog->category->name ?? null,
            'created_by' => $blog->created_by,
            'view_count' => $blog->view_count,
            'created_at' => $blog->created_at->toIso8601String(),
        ];
    }
}
