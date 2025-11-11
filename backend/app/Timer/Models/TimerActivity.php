<?php

namespace App\Timer\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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

    protected function pausedAt(): Attribute
    {
        return Attribute::make(
            set: fn (Carbon $value) => $value->startOfSecond(),
        );
    }

    protected function resumedAt(): Attribute
    {
        return Attribute::make(
            set: fn (Carbon $value) => $value->startOfSecond(),
        );
    }

    public function timer(){
        return $this->belongsTo(Timer::class);
    }
}
