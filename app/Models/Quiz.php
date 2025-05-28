<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Quiz extends Model
{
    protected $fillable = [
        'title',
        'quizable_type',
        'quizable_id',
        'retry',
        'is_active',
        'mix'
    ];

    protected $casts = [
        'retry' => 'boolean',
        'is_active' => 'boolean',
        'mix' => 'boolean',
    ];

    public function quizable(): MorphTo
    {
        return $this->morphTo();
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class, 'quiz_id');
    }
}