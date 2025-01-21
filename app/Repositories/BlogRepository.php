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
        return $this->blog->with('category')->get();
    }

    public function findBySlug($slug){
        
        return $this->blog->with('category')->where("blog_slug","=", $slug)->first();
    }

    public function findById(int $id)
    {
        return $this->blog->with('category')->find($id);
    }

    public function paginate(int $perPage = 15)
    {
        return $this->blog->with('category')->paginate($perPage);
    }
    public function search(string $keyword, int $perPage = 15)
    {
        return $this->blog->with('category')->where('title', 'like', "%$keyword%")
            ->orWhere('blog_content', 'like', "%$keyword%")
            ->paginate($perPage);
    }

    public function getRecent(int $limit = 5)
    {
        return $this->blog->with('category')->orderBy('created_at', 'desc')->take($limit)->get();
    }
}
