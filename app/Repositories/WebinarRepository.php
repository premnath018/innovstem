<?php

namespace App\Repositories;

use App\Models\Webinar;   

class WebinarRepository {
    
    protected $webinar;

    public function __construct(Webinar $webinar){
        $this->webinar = $webinar;
    }

    public function getAll()
    {
        return $this->webinar->with('category')->get();
    }

    public function findBySlug($slug){
        return $this->webinar->with('category')->where("webinar_slug","=", $slug)->first();
    }

    public function findById(int $id)
    {
        return $this->webinar->with('category')->find($id);
    }

    public function paginate(int $perPage = 15)
    {
        return $this->webinar->with('category')->paginate($perPage);
    }

    public function search(string $keyword, int $perPage = 15)
    {
        return $this->webinar->with('category')->where('title', 'like', "%$keyword%")
            ->orWhere('webinar_content', 'like', "%$keyword%")
            ->paginate($perPage);
    }

    public function getRecent(int $limit = 5)
    {
        return $this->webinar->with('category')->with('category')
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }
}




