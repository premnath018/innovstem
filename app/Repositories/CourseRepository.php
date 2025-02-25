<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Course;

class CourseRepository 
{
    protected $course;

    public function __construct(Course $course)
    {
        $this->course = $course;
    }

    public function getAll()
    {
        return $this->course->with('classLevel', 'category')
            ->where('active', true)
            ->whereHas('category', function ($query) {
                $query->where('active', true);
            })
            ->get();
    }

    public function getAllCategories()
    {
        return Category::where('name', '!=', 'No Category')
            ->where('active', true)
            ->get();
    }

    public function findBySlug($slug)
    {
        return $this->course
            ->with(['category', 'classLevel'])
            ->where('course_slug', $slug)
            ->where('active', true)
            ->whereHas('category', function ($query) {
                $query->where('active', true);
            })
            ->first();
    }

    public function findById(int $id)
    {
        return $this->course
            ->with(['category', 'classLevel'])
            ->where('id', $id)
            ->where('active', true)
            ->whereHas('category', function ($query) {
                $query->where('active', true);
            })
            ->first();
    }

    public function getEnrolledCourses(int $studentId)
    {
        return \App\Models\CourseEnrollment::where('student_id', $studentId)
            ->whereHas('course', function ($query) {
                $query->where('active', true)
                    ->whereHas('category', function ($query) {
                        $query->where('active', true);
                    });
            })
            ->with(['course.category', 'course.classLevel'])
            ->get();
    }

    public function paginate(int $perPage = 15)
    {
        return $this->course->with('classLevel', 'category')
            ->where('active', true)
            ->whereHas('category', function ($query) {
                $query->where('active', true);
            })
            ->paginate($perPage);
    }

    public function getPaginatedCourses(?string $categorySlug = null, int $perPage = 9)
    {
        $query = $this->course->with('classLevel', 'category')
            ->where('active', true)
            ->whereHas('category', function ($query) {
                $query->where('active', true);
            });

        if ($categorySlug) {
            $query->whereHas('category', function ($query) use ($categorySlug) {
                $query->where('slug', $categorySlug)
                      ->where('active', true);
            });
        }

        return $query->paginate($perPage);
    }

    public function search(string $keyword, int $perPage = 15)
    {
        return $this->course->with('classLevel', 'category')
            ->where('active', true)
            ->whereHas('category', function ($query) {
                $query->where('active', true);
            })
            ->where(function ($query) use ($keyword) {
                $query->where('title', 'like', "%$keyword%")
                    ->orWhere('course_content', 'like', "%$keyword%");
            })
            ->paginate($perPage);
    }

    public function getRecent(int $limit = 5)
    {
        return $this->course->with('classLevel', 'category')
            ->where('active', true)
            ->whereHas('category', function ($query) {
                $query->where('active', true);
            })
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    public function getCoursesByCategory(int $categoryId, int $limit = 5)
    {
        return $this->course
            ->with('classLevel', 'category')
            ->where('category_id', $categoryId)
            ->where('active', true)
            ->whereHas('category', function ($query) {
                $query->where('active', true);
            })
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    public function getCoursesByCategorySlug(string $categorySlug, int $limit = 5)
    {
        return $this->course
            ->with('classLevel', 'category')
            ->whereHas('category', function ($query) use ($categorySlug) {
                $query->where('slug', $categorySlug)
                      ->where('active', true);
            })
            ->where('active', true)
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }
}
