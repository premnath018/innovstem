<?php

namespace App\Repositories;

use App\Models\Resource;

class ResourceRepository{

    protected $resource;

    public function __construct(Resource $resource){
        $this->resource = $resource;
    }

    public function getAll()
    {
        return $this->resource->with('category')->get();
    }

    public function findBySlug($slug){
        return $this->resource->with('category')->where("resource_slug","=", $slug)->first();
    }

    public function findById(int $id)
    {
        return $this->resource->with('category')->find($id);
    }

    public function paginate(int $perPage = 15)
    {
        return $this->resource->with('category')->paginate($perPage);
    }
    public function search(string $keyword, int $perPage = 15)
    {
        return $this->resource->with('category')->where('title', 'like', "%$keyword%")
            ->orWhere('resource_content', 'like', "%$keyword%")
            ->paginate($perPage);
    }

    public function getRecent(int $limit = 5)
    {
        return $this->resource->with('category')->orderBy('created_at', 'desc')->take($limit)->get();
    }

}
