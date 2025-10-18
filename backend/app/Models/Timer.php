<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timer extends Model
{
    /** @use HasFactory<\Database\Factories\TimerFactory> */
    use HasFactory;

    protected $guarded = [];

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
