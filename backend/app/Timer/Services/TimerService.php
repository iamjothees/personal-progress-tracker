<?php
declare(strict_types=1);

namespace App\Timer\Services;

use App\Models\User;
use App\Timer\Exceptions\TimerActionException;
use App\Timer\Models\Timer;
use App\Timer\Models\TimerActivity;
use Illuminate\Database\Eloquent\Collection AS EloquentCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Context;
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
        $timer = $owner->timers()->create([ 'started_at' => Context::get('now') ]);

        return $timer;
    }

    /**
     * Pause a running timer.
     *
     * @param Timer $timer The timer to pause.
     * @param int|null $secondsElapsed The duration of the current active segment (seconds since start or last resume).
     *                                 If provided, it is added to the last `resumed_at` (or `started_at` if no activities) 
     *                                 to calculate the precise `paused_at` timestamp.
     * @return Timer
     * @throws TimerActionException
     */
    public function pauseTimer(Timer $timer, ?int $secondsElapsed = null): Timer{
        return DB::transaction(function () use ($timer, $secondsElapsed) {
            // Lock the timer for update to prevent race conditions
            $timer = Timer::lockForUpdate()->find($timer->id);
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

            $pausedAt = $secondsElapsed !== null
                ? (
                    $timer->latestActivity
                        ? $timer->latestActivity->resumed_at
                        : $timer->started_at 
                )->startOfSecond()->addSeconds($secondsElapsed)
                : Context::get('now');
            $timer->activities()->create([ 'paused_at' => $pausedAt ]);
            return $timer->fresh();
        });
    }

    /**
     * Resume a paused timer.
     *
     * @param TimerActivity $timerActivity The paused activity to resume.
     * @param int|null $secondsElapsed The duration of the pause (seconds since pause started).
     *                                 If provided, used to calculate the precise `resumed_at` timestamp.
     * @return Timer
     * @throws TimerActionException
     */
    public function resumeTimer(TimerActivity $timerActivity, ?int $secondsElapsed = null): Timer{
        return DB::transaction(function () use ($timerActivity, $secondsElapsed) {
            // Lock the timer (via the activity) for update
            $timer = Timer::lockForUpdate()->find($timerActivity->timer_id);
            // Reload the activity to ensure we have the latest state
            $timerActivity = $timer->activities()->find($timerActivity->id);

            $validator = Validator::make(
                [
                    'has_not_stopped' => $timer->stopped_at === null,
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

            // secondsElapsed represents the pause duration
            $resumedAt = $secondsElapsed !== null ? $timerActivity->paused_at->startOfSecond()->addSeconds($secondsElapsed) : Context::get('now');
            $timerActivity->update([ 'resumed_at' => $resumedAt ]);

            return $timer->fresh();
        });
    }

    /**
     * Stop a running or paused timer.
     *
     * @param Timer $timer The timer to stop.
     * @param int|null $secondsElapsed The total duration from start (seconds since started_at).
     *                                 If provided, used to calculate the precise `stopped_at` timestamp.
     * @return Timer
     * @throws TimerActionException
     */
    public function stopTimer(Timer $timer, ?int $secondsElapsed = null): Timer{
        return DB::transaction(function () use ($timer, $secondsElapsed) {
            // Lock the timer for update
            $timer = Timer::lockForUpdate()->find($timer->id);

            $validator = Validator::make(
                [ 'stopped_at' => $timer->stopped_at === null ], 
                [ 'stopped_at' => 'accepted' ],
                [ 'stopped_at' => "Timer already stopped" ]
            );

            if ($validator->fails()) {
                throw new TimerActionException($validator->errors()->first());
            }

            $stoppedAt = $secondsElapsed !== null ? $timer->started_at->startOfSecond()->addSeconds($secondsElapsed) : Context::get('now');
            $timer->update([ 'stopped_at' => $stoppedAt ]);
            return $timer->fresh();
        });
    }

    /**
     * Reset (delete) a timer and its activities.
     *
     * @param Timer $timer The timer to reset.
     * @return void
     */
    public function resetTimer(Timer $timer): void{
        $timer->loadMissing('activities');
        $timer->activities->each->delete();
        $timer->delete();
        return;
    }

    /**
     * Add trackable entities (projects/tasks) to a timer.
     *
     * @param Timer $timer The timer to update.
     * @param array $timeTrackables Array of trackables with 'type' and 'id'.
     * @return Timer
     */
    public function addTimeTrackables(Timer $timer, array $timeTrackables): Timer{
        $timer->matrices()->upsert(
            collect($timeTrackables)->map(fn ($timeTrackable) => [
                'time_trackable_type' => $timeTrackable['type'],
                'time_trackable_id' => $timeTrackable['id'],
                'timer_id' => $timer->id,
            ])->toArray(),
            [
                'time_trackable_type',
                'time_trackable_id',
                'timer_id',
            ],
        );

        return $timer->load('matrices.timeTrackable');
    }
}
