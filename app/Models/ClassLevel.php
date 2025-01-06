<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassLevel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Define a relationship with courses.
     * Assumes each course has a `class_level_id`.
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
