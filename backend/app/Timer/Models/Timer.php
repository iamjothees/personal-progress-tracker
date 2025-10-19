<?php

namespace App\Timer\Models;

use App\Models\User;
use App\Timer\Models\TimerActivity;
use Database\Factories\TimerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timer extends Model
{
    /** @use HasFactory<\Database\Factories\TimerFactory> */
    use HasFactory;

    protected $casts = [
        'started_at' => 'datetime:d-M-y h:i:s a',
        'stopped_at' => 'datetime:d-M-y h:i:s a',
        'created_at' => 'datetime:d-M-y h:i:s a',
        'updated_at' => 'datetime:d-M-y h:i:s a',
    ];

    protected $guarded = [];

    protected $with = ['activities', 'latestActivity'];

    protected static function newFactory(): TimerFactory
    {
        return TimerFactory::new();
    }

    public function owner(){
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function activities(){
        return $this->hasMany(TimerActivity::class);
    }

    public function latestActivity(){
        return $this->hasOne(TimerActivity::class)->latestOfMany();
    }
}
