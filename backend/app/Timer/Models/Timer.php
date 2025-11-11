<?php

namespace App\Timer\Models;

use App\Models\TimerMatrix;
use App\Models\User;
use App\Project\Models\Project;
use App\Task\Models\Task;
use App\Timer\Models\TimerActivity;
use Database\Factories\TimerFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Context;

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

    protected static function newFactory(): TimerFactory{
        return TimerFactory::new();
    }

    protected function startedAt(): Attribute
    {
        return Attribute::make(
            set: fn (Carbon $value) => $value->startOfSecond(),
        );
    }

    protected function stoppedAt(): Attribute
    {
        return Attribute::make(
            set: fn (Carbon $value) => $value->startOfSecond(),
        );
    }

    public function getElapsedSecondsAttribute(){
        $this->loadMissing('activities', 'latestActivity');
        $end = $this->stopped_at ?? Context::get('now');
        $secondsTakenWithBreaks = $this->started_at->diffInSeconds($end);

        $breakSeconds = 0;
        foreach ($this->activities as $activity) {
            $breakSeconds += $activity->paused_at->diffInSeconds($activity->resumed_at ?? $end);
        }

        return $secondsTakenWithBreaks - $breakSeconds;
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

    public function matrices(){
        return $this->hasMany(TimerMatrix::class)->with('timeTrackable');
    }

    public function projects(){
        return $this->morphedByMany(
            Project::class,
            'time_trackable',
            'timer_matrix',
        );
    }

    public function tasks(){
        return $this->morphedByMany(
            Task::class,
            'time_trackable',
            'timer_matrix',
        );
    }
}
