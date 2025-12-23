<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'due_date',
        'service_id',
        'automation_status',
        'grace_notified_at',
        'last_status_at',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'grace_notified_at' => 'datetime',
        'last_status_at' => 'datetime',
    ];
}
