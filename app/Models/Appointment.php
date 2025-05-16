<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $fillable = [
        'name',
        'mobile_number',
        'email',
        'class',
        'gender',
        'ambition',
        'user_type',
        'package_id',
        'slot_id',
        'transaction_id',
        'amount_paid',
        'payment_status',
        'note',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'amount_paid' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(CounselingPackage::class, 'package_id');
    }

    public function slot(): BelongsTo
    {
        return $this->belongsTo(Slot::class, 'slot_id');
    }
}