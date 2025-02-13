<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebinarAttendance extends Model
{
    protected $fillable = ['student_id', 'webinar_id', 'attended_at'];

    public $timestamps = false; // We use 'attended_at' instead of default timestamps

    protected $casts = [
        'attended_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function webinar(): BelongsTo
    {
        return $this->belongsTo(Webinar::class);
    }
}
