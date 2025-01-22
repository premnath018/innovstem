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
    public function getWebinarBySlug(string $slug)
    {
        $webinar = $this->webinarRepository->findBySlug($slug);
        
        if (!$webinar) {
            throw new \Exception('Webinar not found');
        }
        
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
    }

    /**
     * Get recent webinars with limited fields.
     */
    public function getRecentWebinars(int $limit = 5)
    {
        $RecentWebinars = $this->webinarRepository->getRecent($perPage);

        // Transform the collection inside the paginator
        $transformedWebinars = $RecentWebinars->getCollection()->map(function ($blog) {
            return $this->transformWebinar($blog);
        });

        // Replace the paginator's collection with the transformed data
        $RecentWebinars->setCollection($transformedWebinars);

        return $RecentWebinars;
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
            'created_by' => $webinar->created_by,
            'view_count' => $webinar->view_count,
            'created_at' => $webinar->created_at->toIso8601String(),
        ];
    }
}
