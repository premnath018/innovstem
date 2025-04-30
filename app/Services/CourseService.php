<?php

namespace App\Services;

use App\Models\Category;
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
    public function getCourseBySlug(string $slug, ?int $studentId = null)
    {

        
        $course = $this->courseRepository->findBySlug($slug);
    
        if (!$course) {
            throw new \Exception('Course not found');
        }
    
        // Increment view count
        $course->increment('view_count');
    
        // Initialize variables
        $userRegistered = false;
        $quizScores = [];
        $quizInfo = null;
    

        if ($studentId) {
            $userRegistered = $course->enrolledStudents()
                ->where('student_id', $studentId)
                ->exists();
        }
        // Eager load quizzes with questions and quizAttempts (with optional filtering)
        $quizzes = $course->quizzes()
            ->with(['questions', 'quizAttempts' => function ($query) use ($studentId) {
                if ($studentId) {
                    $query->where('student_id', $studentId);
                }
            }])
            ->get();
    
            $quizData = [];

            if ($quizzes->isNotEmpty()) {
                foreach ($quizzes as $quiz) {
                    $score = -1;
            
                    if ($studentId && $quiz->quizAttempts->isNotEmpty()) {
                        $attempt = $quiz->quizAttempts->firstWhere('student_id', $studentId);
                        $score = $attempt?->score;
                    }
            
                    $quizData[] = [
                        'quiz_id' => $quiz->id,
                        'quiz_title' => $quiz->title,
                        'number_of_questions' => $quiz->questions->count(),
                        'score' => $score,
                    ];
                }
            }
            
            $course->quiz = $quizData;
            
    
        $course->quiz = $quizData;
        $course->user_registered = $userRegistered;
        $course->category_name = $course->category->name ?? null;
        $course->class_level_name = $course->classLevel->name ?? null;
    
        unset($course->classLevel);
        unset($course->category);
        unset($course->quizzes);
    
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
    public function getPaginatedCourses(?String $categoryId = null, int $perPage = 9)
    {
        $paginatedCourses = $this->courseRepository->getPaginatedCourses($categoryId, $perPage);

        // Transform the collection inside the paginator
        $transformedCourses = $paginatedCourses->getCollection()->map(function ($blog) {
            return $this->transformCourse($blog);
        });

        // Replace the paginator's collection with the transformed data
        $paginatedCourses->setCollection($transformedCourses);

        return $paginatedCourses;
    }

    public function allCategroies(){
        return $this->courseRepository->getAllCategories();
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

    public function searchCourses(string $keyword, int $perPage = 9)
    {
        $paginatedCourses = $this->courseRepository->search($keyword, $perPage);
    
        // Transform the collection inside the paginator
        $transformedCourses = $paginatedCourses->getCollection()->map(function ($course) {
            return $this->transformcourse($course);
        });
    
        // Replace the paginator's collection with the transformed data
        $paginatedCourses->setCollection($transformedCourses);
    
        return $paginatedCourses;
    }

    public function getCoursesByCategory($categorySlug, int $limit = 5)
    {
       $paginatedCourses = $this->courseRepository->getCoursesByCategorySlug($categorySlug, $limit);

    // Fetch the category details
    $category = Category::where('slug', $categorySlug)
        ->where('active', true)
        ->firstOrFail();

        if (!$category) {
            throw new \Exception('Category not found');
        }
    // Transform the courses
    $transformedCourses = $paginatedCourses->map(function ($course) {
        return $this->transformCourse($course);
    });

  
    return [
        'category' => [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'short_description' => $category->short_description,
            'long_description' => $category->long_description,
            'image_url' => $category->image_url
        ],
        'courses' => $transformedCourses
    ];
    }

    /**
     * Transform a course to include only the required fields.
     */
    protected function transformCourse($course, ?int $studentId = null)
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
            'user_registered' => $course->user_registered ?? false,
            'quiz_score' => $course->quiz_score ?? null,
        ];
    }
    
}
