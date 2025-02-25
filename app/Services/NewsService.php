<?php

namespace App\Services;

use App\Repositories\NewsRepository;

class NewsService
{
    protected $newsRepository;

    public function __construct(NewsRepository $newsRepository)
    {
        $this->newsRepository = $newsRepository;
    }

    public function getNews(int $limit = 5)
    {
        return $this->newsRepository->getActiveNews($limit);
    }
}
