<?php

namespace App\Timer\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimerActivity extends Model
{
    /** @use HasFactory<\Database\Factories\TimerActivityFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $casts = [
        'paused_at' => 'datetime:d-M-y h:i:s a',
        'resumed_at' => 'datetime:d-M-y h:i:s a',
    ];

    protected $guarded = [];

    public function timer(){
        return $this->belongsTo(Timer::class);
    }
}
