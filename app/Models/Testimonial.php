<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'testimonial_name',
        'designation',
        'testimonial',
        'photo_url',
        'active',
    ];
    
}
