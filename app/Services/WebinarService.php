<?php

namespace App\Services;

use App\Repositories\WebinarRepository;

class WebinarService
{
    protected $webinarRepository;

    public function __construct(WebinarRepository $webinarRepository)
    {
        $this->webinarRepository = $webinarRepository;
    }

    /**
     * Find a webinar by slug.
     */
    public function getWebinarBySlug(string $slug,  ?int $studentId = null)
    {
        $webinar = $this->webinarRepository->findBySlug($slug);
        

        $userRegistered = false;

        if ($studentId) {
            $userRegistered = $webinar->attendedStudents()
                ->where('student_id', $studentId)
                ->exists();
        }

        if (!$webinar) {
            throw new \Exception('Webinar not found');
        }
        

        $webinar->user_registered = $userRegistered;
        $webinar->increment('view_count');
        $webinar->category_name = $webinar->category->name;
        unset($webinar->category);

        return $webinar;
    }

    /**
     * Find a webinar by ID.
     */
    public function getWebinarById(int $id)
    {
        $webinar = $this->webinarRepository->findById($id);

        if (!$webinar) {
            throw new \Exception('Webinar not found');
        }

        return $webinar;
    }

    /**
     * Get all webinars with limited fields.
     */
    public function getAllWebinars()
    {
        return $this->webinarRepository->getAll()->map(function ($webinar) {
            return $this->transformWebinar($webinar);
        });
    }

    /**
     * Get paginated webinars with limited fields.
     */
    public function getPaginatedWebinars(int $perPage = 15)
    {
        $paginatedWebinars = $this->webinarRepository->paginate($perPage);

        // Transform the collection inside the paginator
        $transformedWebinars = $paginatedWebinars->getCollection()->map(function ($blog) {
            return $this->transformWebinar($blog);
        });

        // Replace the paginator's collection with the transformed data
        $paginatedWebinars->setCollection($transformedWebinars);

        return $paginatedWebinars;
    }

    /**
     * Get recent webinars with limited fields.
     */
    public function getRecentWebinars(int $limit = 5)
    {
        return $this->webinarRepository->getRecent($limit)->map(function ($webinars) {
            return $this->transformWebinar($webinars);
        });
    }

    public function searchWebinars(string $keyword, int $perPage = 9)
    {
        $paginatedWebinars = $this->webinarRepository->search($keyword, $perPage);
    
        // Transform the collection inside the paginator
        $transformedWebinars = $paginatedWebinars->getCollection()->map(function ($webinar) {
            return $this->transformWebinar($webinar);
        });
    
        // Replace the paginator's collection with the transformed data
        $paginatedWebinars->setCollection($transformedWebinars);
    
        return $paginatedWebinars;
    }

    /**
     * Transform a webinar to include only the required fields.
     */
    protected function transformWebinar($webinar)
    {
        return [
            'id' => $webinar->id,
            'webinar_slug' => $webinar->webinar_slug,
            'title' => $webinar->title,
            'webinar_description' => $webinar->webinar_description,
            'webinar_thumbnail' => $webinar->webinar_thumbnail,
            'category_name' => $webinar->category->name ?? null,
            'webinar_date_time' => $webinar->webinar_date_time,
            'view_count' => $webinar->view_count,
            'created_by' => $webinar->created_by,
            'view_count' => $webinar->view_count,
            'created_at' => $webinar->created_at->toIso8601String(),
            'updated_at'=> $webinar->updated_at->toIso8601String(),
        ];
    }
}
