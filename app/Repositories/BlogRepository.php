<?php

namespace App\Repositories;

use App\Models\Blog;

class BlogRepository
{
    protected $blog;

    public function __construct(Blog $blog)
    {
        $this->blog = $blog;
    }

    public function getAll()
    {
        return $this->blog->with('category')->where('active', true)->get();
    }

    public function findBySlug($slug)
    {
        return $this->blog->with('category')
            ->where('blog_slug', $slug)
            ->where('active', true)
            ->first();
    }

    public function findById(int $id)
    {
        return $this->blog->with('category')
            ->where('id', $id)
            ->where('active', true)
            ->first();
    }

    public function paginate(int $perPage = 15)
    {
        return $this->blog->with('category')
            ->where('active', true)
            ->paginate($perPage);
    }

    public function search(string $keyword, int $perPage = 9)
    {
        return $this->blog->with('category')
            ->where('active', true)
            ->where(function ($query) use ($keyword) {
                $query->where('title', 'like', "%$keyword%")
                    ->orWhere('blog_content', 'like', "%$keyword%");
            })
            ->paginate($perPage);
    }

    public function getRecent(int $limit = 5)
    {
        return $this->blog->with('category')
            ->where('active', true)
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }
}
