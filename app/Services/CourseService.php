<?php

namespace App\Services;

use App\Repositories\CourseRepository;

class CourseService
{
    protected $courseRepository;

    public function __construct(CourseRepository $courseRepository)
    {
        $this->courseRepository = $courseRepository;
    }

    /**
     * Find a course by slug.
     */
    public function getCourseBySlug(string $slug)
    {
        $course = $this->courseRepository->findBySlug($slug);
       
        if (!$course) {
            throw new \Exception('Course not found');
        }
        
        $quizExists = $course->quizzes()->exists();

        $course->increment('view_count');
        $course->quiz = $quizExists;
        $course->category_name = $course->category->name;
        $course->class_level_name = $course->classLevel->name;
      //   dd($course);
        unset($course->classLevel);
        unset($course->category);

        return $course;
    }

    /**
     * Find a course by ID.
     */
    public function getCourseById(int $id)
    {
        $course = $this->courseRepository->findById($id);

        if (!$course) {
            throw new \Exception('Course not found');
        }

        return $course;
    }

    /**
     * Get all courses with limited fields.
     */
    public function getAllCourses()
    {
        return $this->courseRepository->getAll()->map(function ($course) {
            return $this->transformCourse($course);
        });
    }

    /**
     * Get paginated courses with limited fields.
     */
    public function getPaginatedCourses(int $perPage = 15)
    {
        $paginatedCourses = $this->courseRepository->paginate($perPage);

        // Transform the collection inside the paginator
        $transformedCourses = $paginatedCourses->getCollection()->map(function ($blog) {
            return $this->transformCourse($blog);
        });

        // Replace the paginator's collection with the transformed data
        $paginatedCourses->setCollection($transformedCourses);

        return $paginatedCourses;
    }

    /**
     * Get recent courses with limited fields.
     */
    public function getRecentCourses(int $limit = 5)
    {
        return $this->courseRepository->getRecent($limit)->map(function ($course) {
            return $this->transformCourse($course);
        });
    }

    /**
     * Transform a course to include only the required fields.
     */
    protected function transformCourse($course)
    {
        return [
            'id' => $course->id,
            'course_slug' => $course->course_slug,
            'title' => $course->title,
            'content_short_description' => $course->content_short_description,
            'course_thumbnail' => $course->course_thumbnail,
            'category_name' => $course->category->name ?? null,
            'created_by' => $course->created_by,
            'class_level_name' => $course->classLevel->name ?? null,
            'view_count' => $course->view_count,
            'enrolment_count' => $course->enrolment_count,
            'created_at' => $course->created_at->toIso8601String(),
            'updated_at'=> $course->updated_at->toIso8601String(),
        ];
    }
}
