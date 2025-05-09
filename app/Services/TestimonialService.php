<?php
namespace App\Services;

use App\Models\Testimonial;

class TestimonialService
{
    public function getActiveTestimonials(int $limit = 5)
    {
        return Testimonial::where('active', true)
            ->orderByDesc('created_at')
            ->take($limit)
            ->get(['id', 'testimonial_name', 'designation', 'testimonial', 'photo_url']);
    }
}
