<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Webinar extends Model
{
    protected $fillable = ['title', 'url'];

    public function quizzes(): MorphMany
    {
        return $this->morphMany(Quiz::class, 'quizable');
    }
}