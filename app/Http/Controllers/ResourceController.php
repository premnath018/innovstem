<?php

namespace App\Http\Controllers;

use App\Services\ResourceService;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class ResourceController extends Controller
{
    protected $ResourceService;

    public function __construct(ResourceService $ResourceService)
    {
        $this->ResourceService = $ResourceService;
    }

    public function paginate()
    {
        try {
            $Resources = $this->ResourceService->getPaginatedResources(9);
            return ApiResponse::success($Resources, 'Paginated Resources retrieved successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    public function show($slug)
    {
        try {
            $resource = $this->ResourceService->getResourceBySlug($slug);

            if ($resource->type === 'paid' && !$resource->has_access) {
                return ApiResponse::error('You do not have access to this paid resource.', 403);
            }

            return ApiResponse::success($resource, 'Resource retrieved successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 404);
        }
    }

    public function recent()
    {
        try {
            $Resources = $this->ResourceService->getRecentResources(5);
            return ApiResponse::success($Resources, 'Recent Resources retrieved successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $request->validate([
                'keyword' => 'required|string|min:2',
                'perPage' => 'nullable|integer|min:1'
            ]);

            $keyword = $request->input('keyword');
            $perPage = $request->input('perPage', 9);

            $Resource = $this->ResourceService->searchResource($keyword, $perPage);

            return ApiResponse::success($Resource, 'Search results retrieved successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }
}