<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareerApplication extends Model
{
    use HasFactory;

    protected $table = 'careers_application'; // Match the migration table name

    protected $fillable = [
        'career_id',
        'applicant_name',
        'email',
        'phone',
        'cover_letter',
        'resume_path',
        'status',
    ];

    protected $casts = [
        'status' => 'string', // Enum as string
    ];

    public function career()
    {
        return $this->belongsTo(Career::class, 'career_id');
    }
}