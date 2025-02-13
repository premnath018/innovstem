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

    public function findBySlug($slug)
    {
        return $this->course
            ->with(['category', 'classLevel']) // Eager Load
            ->where("course_slug", "=", $slug)
            ->where('active', true)
            ->first();
    }

    public function findById(int $id)
    {
        return $this->course
            ->with(['category', 'classLevel']) // Eager Load
            ->find($id);
    }

    public function getEnrolledCourses(int $studentId)
    {
        return \App\Models\CourseEnrollment::where('student_id', $studentId)
            ->with(['course.category', 'course.classLevel']) // Eager Load
            ->get();
    }


    public function paginate(int $perPage = 15)
    {
        return $this->course->with('classLevel','category')->paginate($perPage);
    }

    public function getPaginatedCourses(?string $categorySlug = null, int $perPage = 9)
    {
        $query = $this->course->with('classLevel', 'category');
    
        if ($categorySlug) {
            $query->whereHas('category', function ($query) use ($categorySlug) {
                $query->where('slug', $categorySlug);
            });
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

    public function getCoursesByCategorySlug(string $categorySlug, int $limit = 5)
    {
        return $this->course
            ->with('classLevel', 'category')
            ->whereHas('category', function ($query) use ($categorySlug) {
                $query->where('slug', $categorySlug);
            })
            ->where('active', true)
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

}