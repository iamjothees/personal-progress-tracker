<?php

namespace App\Models;

use App\Project\Models\Project;
use App\Task\Models\Task;
use App\Timer\Models\Timer;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class TimerMatrix extends MorphPivot
{
    public $timestamps = false;

    public function timer(){
        return $this->belongsTo(Timer::class);
    }

    public function timeTrackable(){
        return $this->morphTo();
    }

    public static function timeTrackables(): array{
        return [
            'project' => [ 'class' => Project::class ],
            'task' => [ 'class' => Task::class ],
        ];
    }
}
