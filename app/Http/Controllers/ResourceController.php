<?php

namespace App\Http\Controllers;

use App\Services\ResourceService;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    protected $ResourceService;

    public function __construct(ResourceService $ResourceService)
    {
        $this->ResourceService = $ResourceService;
    }

    /**
     * Get paginated Resources.
     */
    public function paginate()
    {
        try {
            $Resources = $this->ResourceService->getPaginatedResources(9); // Default to 15 items per page
            return ApiResponse::success($Resources, 'Paginated Resources retrieved successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    /**
     * Get a Resource by slug.
     */
    public function show($slug)
    {
        try {
            $Resource = $this->ResourceService->getResourceBySlug($slug);
            return ApiResponse::success($Resource, 'Resource retrieved successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 404);
        }
    }

    /**
     * Get recent Resources.
     */
    public function recent()
    {
        try {
            $Resources = $this->ResourceService->getRecentResources(5); // Default to 5 recent Resources
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
