<?php
declare(strict_types=1);

namespace App\Timer\Services;

use App\Models\User;
use App\Timer\Exceptions\TimerActionException;
use App\Timer\Models\Timer;
use App\Timer\Models\TimerActivity;
use Illuminate\Database\Eloquent\Collection AS EloquentCollection;
use Illuminate\Support\Facades\Validator;

class TimerService
{
    public function __construct() {}

    public function getTimers(User $owner, bool $withStopped = false): EloquentCollection{
        $timers = $owner->timers()
            ->when( $withStopped === false, fn ($q) => $q->whereNull('stopped_at') )
            ->get();

        return $timers;
    }

    public function startTimer(User $owner): Timer{
        $timer = $owner->timers()->create([ 'started_at' => now() ]);

        return $timer;
    }

    public function pauseTimer(Timer $timer): Timer{
        $timer->loadMissing('latestActivity');
        $validator = Validator::make(
            [ 
                'has_not_stopped' => $timer->stopped_at === null,
                'has_not_paused' => $timer->latestActivity === null || $timer->latestActivity->resumed_at !== null,
            ], 
            [ 
                'has_not_stopped' => 'accepted',
                'has_not_paused' => 'accepted' 
            ],
            [ 
                'has_not_stopped' => 'Timer already stopped',
                'has_not_paused' => 'Timer already paused' 
            ]
        );

        if ($validator->fails()) {
            throw new TimerActionException($validator->errors()->first());
        }

        $timer->activities()->create([ 'paused_at' => now() ]);

        return $timer->fresh();
    }

    public function resumeTimer(TimerActivity $timerActivity): Timer{
        $timerActivity->loadMissing('timer');
        $validator = Validator::make(
            [
                'has_not_stopped' => $timerActivity->timer->stopped_at === null,
                'has_not_resumed' => $timerActivity->resumed_at === null,
            ], 
            [
                'has_not_stopped' => 'accepted',
                'has_not_resumed' => 'accepted' 
            ],
            [ 
                'has_not_stopped' => 'Timer already stopped',
                'has_not_resumed' => 'Timer Activity already resumed' 
            ]
        );

        if ($validator->fails()) {
            throw new TimerActionException($validator->errors()->first());
        }

        $timerActivity->update([ 'resumed_at' => now() ]);

        return $timerActivity->timer->fresh();
    }

    public function stopTimer(Timer $timer): Timer{
        $validator = Validator::make(
            [ 'stopped_at' => $timer->stopped_at === null ], 
            [ 'stopped_at' => 'accepted' ],
            [ 'stopped_at' => "Timer already stopped" ]
        );

        if ($validator->fails()) {
            throw new TimerActionException($validator->errors()->first());
        }

        $timer->update([ 'stopped_at' => now() ]);

        return $timer->fresh();
    }
}
