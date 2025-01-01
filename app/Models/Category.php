<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'short_description',
        'long_description',
        'image_url',
    ];

    // Relationships
    public function blogs()
    {
        return $this->hasMany(Blog::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function webinars()
    {
        return $this->hasMany(Webinar::class);
    }

    public function resources()
    {
        return $this->hasMany(Resource::class);
    }
}
