<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Course;

class CourseRepository {

    protected $course;

    public function __construct(Course $course){
        $this->course = $course;
    }

    public function getAll()
    {
        return $this->course->with('classLevel','category')->all();
    }

    public function getAllCategories()
    {
        return Category::where('name','!=','No Category')->get();
    }

    public function findBySlug($slug){
        return $this->course->with('classLevel','category')->where("course_slug","=", $slug)->where('active', true)->first();
    }

    public function findById(int $id)
    {
        return $this->course->with('classLevel','category')->find($id);
    }

    public function paginate(int $perPage = 15)
    {
        return $this->course->with('classLevel','category')->paginate($perPage);
    }

    public function getPaginatedCourses(?int $categoryId = null, int $perPage = 9)
    {
        $query = $this->course->with('classLevel', 'category');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        return $query->paginate($perPage);
    }


    public function search(string $keyword, int $perPage = 15)
    {
        return $this->course->with('classLevel','category')->where('title', 'like', "%$keyword%")
            ->orWhere('course_content', 'like', "%$keyword%")
            ->paginate($perPage);
    }

    public function getRecent(int $limit = 5)
    {
        return $this->course->with('classLevel','category')->orderBy('created_at', 'desc')->take($limit)->get();
    }

    public function getCoursesByCategory(int $categoryId, int $limit = 5)
    {
        return $this->course
            ->with('classLevel', 'category')
            ->where('category_id', $categoryId)
            ->where('active', true)
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }
}