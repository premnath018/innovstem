<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Course extends Model
{
    protected $fillable = ['title', 'description'];

    public function quizzes(): MorphMany
    {
        return $this->morphMany(Quiz::class, 'quizable');
    }
}
