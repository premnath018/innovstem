<?php

namespace App\Services;

use App\Repositories\ResourceRepository;
use App\Models\ResourceTransaction;
use Illuminate\Support\Facades\Auth;

class ResourceService
{
    protected $resourceRepository;

    public function __construct(ResourceRepository $resourceRepository)
    {
        $this->resourceRepository = $resourceRepository;
    }

    public function getResourceBySlug(string $slug)
    {
        $resource = $this->resourceRepository->findBySlug($slug);

        if (!$resource) {
            throw new \Exception('Resource not found');
        }

        $resource->category_name = $resource->category->name ?? null;
        unset($resource->category);

        $resource->has_access = $this->checkUserAccess($resource);
        $resource->quiz = $resource->quizzes()->exists();
        $resource->increment('view_count');

        return $resource;
    }

    public function getResourceById(int $id)
    {
        $resource = $this->resourceRepository->findById($id);

        if (!$resource) {
            throw new \Exception('Resource not found');
        }

        $resource->category_name = $resource->category->name;
        $resource->has_access = $this->checkUserAccess($resource);
        unset($resource->category);

        return $resource;
    }

    public function getAllResources()
    {
        return $this->resourceRepository->getAll()->map(function ($resource) {
            return $this->transformResource($resource);
        });
    }

    public function getPaginatedResources(int $perPage = 15)
    {
        $paginatedResources = $this->resourceRepository->paginate($perPage);

        $transformedResources = $paginatedResources->getCollection()->map(function ($resource) {
            return $this->transformResource($resource);
        });

        $paginatedResources->setCollection($transformedResources);

        return $paginatedResources;
    }

    public function getRecentResources(int $limit = 5)
    {
        return $this->resourceRepository->getRecent($limit)->map(function ($resource) {
            return $this->transformResource($resource);
        });
    }

    public function searchResource(string $keyword, int $perPage = 9)
    {
        $paginatedResource = $this->resourceRepository->search($keyword, $perPage);

        $transformedResource = $paginatedResource->getCollection()->map(function ($resource) {
            return $this->transformResource($resource);
        });

        $paginatedResource->setCollection($transformedResource);

        return $paginatedResource;
    }

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
            'type' => $resource->type,
            'amount' => $resource->amount,
            'has_access' => $this->checkUserAccess($resource), // Add access check
            'created_at' => $resource->created_at->toIso8601String(),
            'updated_at' => $resource->updated_at->toIso8601String(),
        ];
    }

    protected function checkUserAccess($resource)
    {
        if ($resource->type === 'free') {
            return true;
        }

        $user = Auth::user();
        if (!$user) {
            return false;
        }

        return ResourceTransaction::where('user_id', $user->id)
            ->where('resource_id', $resource->id)
            ->where('status', 'paid')
            ->exists();
    }
}