<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CounselingPackage extends Model
{
    protected $fillable = [
        'category',
        'package_name',
        'price_inr',
        'duration',
        'includes',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'price_inr' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}