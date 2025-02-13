<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    protected $fillable = [
        'user_id', 'name', 'mobile', 'standard', 'ambition', 'parent_no',
        'age', 'gender', 'district', 'address', 'state'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function enrolledCourses(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function attendedWebinars(): HasMany
    {
        return $this->hasMany(WebinarAttendance::class);
    }

    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }
}
