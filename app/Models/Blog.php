<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Blog extends Model
{
    protected $fillable = [
        'blog_slug',
        'blog_title',
        'blog_description',
        'blog_content',
        'blog_banner',
        'blog_thumbnail',
        'category_id',
        'created_by',
        'blog_meta_title',
        'blog_meta_keyword',
        'blog_meta_description',
        'view_count',
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
}

