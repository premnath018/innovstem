<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Webinar extends Model
{
    protected $fillable = [
        'webinar_slug',
        'webinar_title',
        'webinar_description',
        'webinar_content',
        'webinar_banner',
        'webinar_thumbnail',
        'category_id',
        'created_by',
        'webinar_meta_title',
        'webinar_meta_keyword',
        'webinar_meta_description',
        'webinar_date_time',
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
