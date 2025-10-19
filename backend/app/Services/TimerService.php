<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Timer;
use App\Models\TimerActivity;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection AS EloquentCollection;
use Illuminate\Support\Facades\Validator;

class TimerService
{
    public function __construct() {}

    public function getTimers(User $owner, bool $withCompleted = false): EloquentCollection{
        $timers = $owner->timers()
            ->when( $withCompleted === false, fn ($q) => $q->whereNull('completed_at') )
            ->get();

        return $timers;
    }

    public function startTimer(User $owner): Timer{
        $timer = $owner->timers()->create([ 'started_at' => now() ]);

        return $timer;
    }

    public function pauseTimer(Timer $timer): TimerActivity{
        $timer->loadMissing('latestActivity');
        $validator = Validator::make(
            [ 
                'has_not_completed' => $timer->completed_at === null,
                'has_not_paused' => $timer->latestActivity === null || $timer->latestActivity->completed_at !== null,
            ], 
            [ 
                'has_not_completed' => 'accepted',
                'has_not_paused' => 'accepted' 
            ],
            [ 
                'has_not_completed' => 'Timer already stopped',
                'has_not_paused' => 'Timer already paused' 
            ]
        );

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        $timerActivity = $timer->activities()->create([ 'paused_at' => now() ]);

        return $timerActivity;
    }

    public function resumeTimer(TimerActivity $timerActivity): TimerActivity{
        $timerActivity->loadMissing('timer');
        $validator = Validator::make(
            [
                'has_not_completed' => $timerActivity->timer->completed_at === null,
                'has_not_resumed' => $timerActivity->resumed_at === null,
            ], 
            [
                'has_not_completed' => 'accepted',
                'has_not_resumed' => 'accepted' 
            ],
            [ 
                'has_not_completed' => 'Timer already stopped',
                'has_not_resumed' => 'Timer Activity already resumed' 
            ]
        );

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        $timerActivity->update([ 'resumed_at' => now() ]);

        return $timerActivity;
    }

    public function stopTimer(Timer $timer): Timer{
        $validator = Validator::make(
            [ 'completed_at' => $timer->completed_at === null ], 
            [ 'completed_at' => 'accepted' ],
            [ 'completed_at' => "Timer already stopped" ]
        );

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        $timer->update([ 'completed_at' => now() ]);

        return $timer;
    }
}
