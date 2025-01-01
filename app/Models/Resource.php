<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $fillable = [
        'resource_slug',
        'resource_title',
        'resource_content',
        'resource_url',
        'resource_description',
        'resource_banner',
        'resource_thumbnail',
        'category_id',
        'created_by',
        'resource_meta_title',
        'resource_meta_keyword',
        'resource_meta_description',
        'view_count',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
