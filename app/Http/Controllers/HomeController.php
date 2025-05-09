<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use App\Models\RouteView;
use App\Services\BlogService;
use App\Services\CourseService;
use App\Services\NewsService;
use App\Services\RecommendationService;
use App\Services\TestimonialService;
use App\Services\WebinarService;

class HomeController extends Controller
{
    protected $blogService;
    protected $courseService;
    protected $webinarService;
    protected $recommendationService;
    protected $newsService;

    protected $testimonialService;

    public function __construct(
        BlogService $blogService,
        CourseService $courseService,
        WebinarService $webinarService,
        NewsService $newsService,
        TestimonialService $testimonialService // Inject here
    ) {
        $this->blogService = $blogService;
        $this->courseService = $courseService;
        $this->webinarService = $webinarService;
        $this->newsService = $newsService;
        $this->testimonialService = $testimonialService;
    }

    /**
     * Get top 5 recent blogs, courses, and webinars.
     */
    public function home()
    {
        try {
            $blogs = $this->blogService->getRecentBlogs(5);
            $courses = $this->courseService->allCategroies();
            $webinars = $this->webinarService->getRecentWebinars(5);
            $news = $this->newsService->getNews(5);
            $testimonials = $this->testimonialService->getActiveTestimonials(5);
    
            return ApiResponse::success([
                'blogs' => $blogs,
                'courses' => $courses,
                'webinars' => $webinars,
                'news' => $news,
                'testimonials' => $testimonials
            ], 'Recent content retrieved successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }
    

    public function recommend(Request $request)
    {
        try {
            $recommendationService = app(RecommendationService::class); // Resolve service manually
    
            $categoryId = $request->input('category_id') ;
            $limit =  $request->input('limit', 5);
    
            $recommendations = $recommendationService->getRecommendations($categoryId, $limit);
    
            return ApiResponse::success($recommendations, 'Recommendations retrieved successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    public function news(){
        try {
           $news = $this->newsService->getNews(5);

            return ApiResponse::success($news, 'Recent content retrieved successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }
    

    public function track(Request $request)
    {
        $request->validate([
            'r' => 'required|string|max:255',
        ]);

        $routeView = RouteView::firstOrCreate(
            ['route' => $request->r],
            ['views' => 0]
        );

        $routeView->increment('views');

        return response('ok');
    }

        public function getTotalViews()
    {
        $total = \App\Models\RouteView::sum('views');

        return response()->json([
            'value' => $total
        ]);
    }
   
}
