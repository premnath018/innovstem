<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Career extends Model
{
    use HasFactory;

    protected $table = 'careers'; // Match the migration table name

    protected $fillable = [
        'title',
        'description',
        'location',
        'employment_type',
        'domain',
        'experience',
        'is_active',
        'registration_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'employment_type' => 'string', // Enum as string
    ];

    public function applications()
    {
        return $this->hasMany(CareerApplication::class, 'career_id');
    }
}