<?php

namespace App\Services;

use App\Repositories\BlogRepository;
use App\Repositories\CourseRepository;
use App\Repositories\WebinarRepository;

class RecommendationService
{
    protected $blogRepository;
    protected $courseRepository;
    protected $webinarRepository;

    public function __construct(
        BlogRepository $blogRepository,
        CourseRepository $courseRepository,
        WebinarRepository $webinarRepository
    ) {
        $this->blogRepository = $blogRepository;
        $this->courseRepository = $courseRepository;
        $this->webinarRepository = $webinarRepository;
    }

    /**
     * Get recommendations based on category presence.
     */ 
    public function getRecommendations(?int $categoryId = null, int $limit = 5)
    {
      
        
            $blogs = $this->blogRepository->getRecent($limit)->map(fn ($blog) => $this->transformBlog($blog));
            $webinars = $this->webinarRepository->getRecent($limit)->map(fn ($webinar) => $this->transformWebinar($webinar));
            if ($categoryId) {
            $courses = $this->courseRepository->getCoursesByCategory($categoryId, $limit)->map(function ($course) {
                return $this->transformCourse($course);
            });
        } else {
            $courses = $this->courseRepository->getRecent($limit)->map(function ($course) {
                return $this->transformCourse($course);
            });
        }
        
            return [
                'recommended_courses' => $courses,
                'recommended_blogs' => $blogs,
                'recommended_webinars' => $webinars,
            ];
        
    }

    /**
     * Transform a course object.
     */
    protected function transformCourse($course)
    {
        return [
            'id' => $course->id,
            'slug' => $course->course_slug,
            'title' => $course->title,
            'content_short_description' => $course->content_short_description,
            'thumbnail' => $course->course_thumbnail,
            'category_name' => $course->category->name ?? null,
            'view_count' => $course->view_count,
            'enrolment_count' => $course->enrolment_count,
            'created_at' => $course->created_at->toIso8601String(),
        ];
    }

    /**
     * Transform a blog object.
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

    /**
     * Transform a webinar object.
     */
    protected function transformWebinar($webinar)
    {
        return [
            'id' => $webinar->id,
            'slug' => $webinar->webinar_slug,
            'title' => $webinar->title,
            'description' => $webinar->webinar_description,
            'thumbnail' => $webinar->webinar_thumbnail,
            'category_name' => $webinar->category->name ?? null,
            'webinar_date_time' => $webinar->webinar_date_time,
            'created_by' => $webinar->created_by,
            'view_count' => $webinar->view_count,
            'created_at' => $webinar->created_at->toIso8601String(),
        ];
    }
}
