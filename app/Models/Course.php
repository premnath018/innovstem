<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Course extends Model
{
    protected $fillable = [
        'course_slug',
        'title',
        'content_short_description',
        'content_long_description',
        'course_content',
        'learning_materials',
        'course_banner',
        'course_thumbnail',
        'category_id',
        'created_by',
        'course_meta_title',
        'course_meta_keyword',
        'course_meta_description',
        'class_level_id',
        'view_count',
        'enrolment_count',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
//        'learning_materials' => 'array',
//        'course_content' => 'array',
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

    public function classLevel()
    {
        return $this->belongsTo(ClassLevel::class);
    }
}
