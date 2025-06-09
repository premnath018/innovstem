<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Resource extends Model
{
    protected $fillable = [
        'resource_slug',
        'title',
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
        'type', // New field
        'amount', // New field
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
    

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function quizzes(): MorphMany
    {
        return $this->morphMany(Quiz::class, 'quizable');
    }

    public function transactions()
    {
        return $this->hasMany(ResourceTransaction::class);
    }
}
