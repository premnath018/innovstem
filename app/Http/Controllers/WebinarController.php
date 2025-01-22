<?php

namespace App\Http\Controllers;

use App\Services\WebinarService;
use App\Helpers\ApiResponse;

class WebinarController extends Controller
{
    protected $WebinarService;

    public function __construct(WebinarService $WebinarService)
    {
        $this->WebinarService = $WebinarService;
    }

    /**
     * Get paginated Webinars.
     */
    public function paginate()
    {
        try {
            $Webinars = $this->WebinarService->getPaginatedWebinars(9); // Default to 15 items per page
            return ApiResponse::success($Webinars, 'Paginated Webinars retrieved successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    /**
     * Get a Webinar by slug.
     */
    public function show($slug)
    {
        try {
            $Webinar = $this->WebinarService->getWebinarBySlug($slug);
            return ApiResponse::success($Webinar, 'Webinar retrieved successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 404);
        }
    }

    /**
     * Get recent Webinars.
     */
    public function recent()
    {
        try {
            $Webinars = $this->WebinarService->getRecentWebinars(5); // Default to 5 recent Webinars
            return ApiResponse::success($Webinars, 'Recent Webinars retrieved successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }
}
