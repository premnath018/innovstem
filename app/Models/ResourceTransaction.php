<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'resource_id',
        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_signature',
        'amount',
        'currency',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }
}