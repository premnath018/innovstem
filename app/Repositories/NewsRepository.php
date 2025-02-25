<?php

namespace App\Repositories;

use App\Models\News;

class NewsRepository
{
    public function getActiveNews(int $limit = 5)
    {
        return News::where('active', true)
            ->orderByDesc('priority')
            ->orderByDesc('created_at')
            ->take($limit)
            ->get();
    }
}
