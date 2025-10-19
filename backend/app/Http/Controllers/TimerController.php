<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Timer;
use App\Models\TimerActivity;
use App\Services\TimerService;
use Illuminate\Support\Facades\Validator;

class TimerController extends Controller
{
    public function __construct(private TimerService $timerService){}

    public function index(): ApiResponse{
        $timers = $this->timerService->getTimers(owner: auth()->user());
        return new ApiResponse(data: ['timers' => $timers->toResourceCollection()]);
    }

    public function view(Timer $timer): ApiResponse{
        return new ApiResponse(data: ['timer' => $timer->toResource()]);
    }


    public function start(): ApiResponse{
        $timer = $this->timerService->startTimer(owner: auth()->user());
        return new ApiResponse(data: ['timer' => $timer->toResource()]);
    }

    public function pause(Timer $timer): ApiResponse{
        $validator = Validator::make(
            [
                'has_latest_activity' => (bool) $timer->latestActivity,
                'same_latest_activity' => $timer->latestActivity?->id === request()->get('latest_activity_id', null),
            ],
            [
                'same_latest_activity' => 'accepted_if:has_latest_activity,accepted',
            ],
            [
                'same_latest_activity' => "Latest activity does not match",
            ]
        );

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        $timer = $this->timerService->pauseTimer(timer: $timer);
        return new ApiResponse(data: ['timer' => $timer->toResource()]);
    }

    public function resume(TimerActivity $timerActivity): ApiResponse{
        $validator = Validator::make(
            [
                'same_latest_activity' => $timerActivity->id === $timerActivity->timer->latestActivity->id,
            ],
            [
                'same_latest_activity' => 'accepted',
            ],
            [
                'same_latest_activity' => "Latest activity does not match",
            ]
        );

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        $timer = $this->timerService->resumeTimer(timerActivity: $timerActivity);
        return new ApiResponse(data: ['timer' => $timer->toResource()]);
    }

    public function stop(Timer $timer): ApiResponse{
        $validator = Validator::make(
            [
                'has_latest_activity' => (bool) $timer->latestActivity,
                'same_latest_activity' => $timer->latestActivity?->id === request()->get('latest_activity_id', null),
            ],
            [
                'same_latest_activity' => 'accepted_if:has_latest_activity,accepted',
            ],
            [
                'same_latest_activity' => "Latest activity does not match",
            ]
        );

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        $timer = $this->timerService->stopTimer(timer: $timer);
        return new ApiResponse(data: ['timer' => $timer->toResource()]);
    }
}
