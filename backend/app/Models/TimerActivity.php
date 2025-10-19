<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimerActivity extends Model
{
    /** @use HasFactory<\Database\Factories\TimerActivityFactory> */
    use HasFactory;

    protected $casts = [
        'paused_at' => 'datetime:d-M-y h:i:s a',
        'resumed_at' => 'datetime:d-M-y h:i:s a',
        'created_at' => 'datetime:d-M-y h:i:s a',
        'updated_at' => 'datetime:d-M-y h:i:s a',
    ];

    protected $guarded = [];
}
