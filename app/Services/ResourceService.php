<?php

namespace App\Services;

use App\Repositories\ResourceRepository;

class ResourceService
{
    protected $resourceRepository;

    public function __construct(ResourceRepository $resourceRepository)
    {
        $this->resourceRepository = $resourceRepository;
    }

    /**
     * Find a resource by slug.
     */
    public function getResourceBySlug(string $slug)
    {
        $resource = $this->resourceRepository->findBySlug($slug);

        $resource->category_name = $resource->category->name ?? null;
        unset($resource->category);

        if (!$resource) {
            throw new \Exception('Resource not found');
        }


        $quizExists = $resource->quizzes()->exists();
        $resource->increment('view_count');
        $resource->quiz = $quizExists;

        return $resource;
    }

    /**
     * Find a resource by ID.
     */
    public function getResourceById(int $id)
    {
        $resource = $this->resourceRepository->findById($id);

        if (!$resource) {
            throw new \Exception('Resource not found');
        }

        $resource->category_name = $resource->category->name;
        unset($resource->category);

        return $resource;
    }

    /**
     * Get all resources with limited fields.
     */
    public function getAllResources()
    {
        return $this->resourceRepository->getAll()->map(function ($resource) {
            return $this->transformResource($resource);
        });
    }

    /**
     * Get paginated resources with limited fields.
     */
    public function getPaginatedResources(int $perPage = 15)
    {
        $paginatedResources = $this->resourceRepository->paginate($perPage);

        // Transform the collection inside the paginator
        $transformedResources = $paginatedResources->getCollection()->map(function ($blog) {
            return $this->transformResource($blog);
        });

        // Replace the paginator's collection with the transformed data
        $paginatedResources->setCollection($transformedResources);

        return $paginatedResources;
    }

    /**
     * Get recent resources with limited fields.
     */
    public function getRecentResources(int $limit = 5)
    {
        return $this->resourceRepository->getRecent($limit)->map(function ($resource) {
            return $this->transformResource($resource);
        });
    }

    public function searchResource(string $keyword, int $perPage = 9)
    {
        $paginatedResource = $this->resourceRepository->search($keyword, $perPage);
    
        // Transform the collection inside the paginator
        $transformedResource = $paginatedResource->getCollection()->map(function ($resource) {
            return $this->transformResource($resource);
        });
    
        // Replace the paginator's collection with the transformed data
        $paginatedResource->setCollection($transformedResource);
    
        return $paginatedResource;
    }

    /**
     * Transform a resource to include only the required fields.
     */
    protected function transformResource($resource)
    {
        return [
            'id' => $resource->id,
            'resource_slug' => $resource->resource_slug,
            'title' => $resource->title,
            'resource_description' => $resource->resource_description,
            'resource_thumbnail' => $resource->resource_thumbnail,
            'category_name' => $resource->category->name ?? null,
            'view_count' => $resource->view_count,
            'created_by' => $resource->created_by,
            'view_count' => $resource->view_count,
            'created_at' => $resource->created_at->toIso8601String(),
            'updated_at'=> $resource->updated_at->toIso8601String(),
        ];
    }
}
