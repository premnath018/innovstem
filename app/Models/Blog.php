<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Blog extends Model
{
    protected $fillable = ['title', 'content'];

    public function quizzes(): MorphMany
    {
        return $this->morphMany(Quiz::class, 'quizable');
    }
}

