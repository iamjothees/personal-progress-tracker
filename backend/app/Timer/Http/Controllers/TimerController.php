<?php

namespace App\Timer\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Timer\Exceptions\TimerActionException;
use App\Timer\Http\Requests\AddTrackablesRequest;
use App\Timer\Http\Requests\PauseTimerRequest;
use App\Timer\Http\Requests\ResumeTimerRequest;
use App\Timer\Http\Requests\StopTimerRequest;
use App\Timer\Models\Timer;
use App\Timer\Models\TimerActivity;
use App\Timer\Services\TimerService;
use Exception;
use Illuminate\Validation\ValidationException;

class TimerController extends Controller
{
    public function __construct(private TimerService $timerService){}

    public function index(): ApiResponse{
        $timers = $this->timerService->getTimers(owner: auth()->user());
        return new ApiResponse(data: ['timers' => []]); // dev
        return new ApiResponse(data: ['timers' => $timers->toResourceCollection()]);
    }

    public function view(Timer $timer): ApiResponse{
        $timer->load('projects', 'tasks');
        return new ApiResponse(data: ['timer' => $timer->toResource()]);
    }


    public function start(): ApiResponse{
        $timer = $this->timerService->startTimer(owner: auth()->user());
        return new ApiResponse(data: ['timer' => $timer->toResource()]);
    }

    public function pause(PauseTimerRequest $request, Timer $timer): ApiResponse{
        try {
            $timer = $this->timerService->pauseTimer(timer: $timer, secondsElapsed: $request->seconds_elapsed);
        } catch (TimerActionException $e) {
            throw ValidationException::withMessages(['timer' => $e->getMessage()]);
        }

        return new ApiResponse(data: ['timer' => $timer->toResource()]);
    }

    public function resume(ResumeTimerRequest $request, Timer $timer, TimerActivity $timerActivity): ApiResponse{
        // throw new Exception('test'); // dev
        try {
            $timer = $this->timerService->resumeTimer(timerActivity: $timerActivity, secondsElapsed: $request->seconds_elapsed);
        } catch (TimerActionException $e) {
            throw ValidationException::withMessages(['timer' => $e->getMessage()]);
        }

        return new ApiResponse(data: ['timer' => $timer->toResource()]);
    }

    public function stop(StopTimerRequest $request, Timer $timer): ApiResponse{
        try {
            $timer = $this->timerService->stopTimer(timer: $timer, secondsElapsed: $request->seconds_elapsed);
        } catch (TimerActionException $e) {
            throw ValidationException::withMessages(['timer' => $e->getMessage()]);
        }

        return new ApiResponse(data: ['timer' => $timer->toResource()]);
    }

    public function reset(Timer $timer): ApiResponse{
        try {
            $this->timerService->resetTimer(timer: $timer);
        } catch (TimerActionException $e) {
            throw ValidationException::withMessages(['timer' => $e->getMessage()]);
        }

        return new ApiResponse();
    }

    public function addTimeTrackables(AddTrackablesRequest $request, Timer $timer): ApiResponse{
        try {
            $timer = $this->timerService->addTimeTrackables(timer: $timer, timeTrackables: $request->validated('time_trackables'));
        } catch (TimerActionException $e) {
            throw ValidationException::withMessages(['timer' => $e->getMessage()]);
        }

        return new ApiResponse(data: ['timer' => $timer->toResource()]);
    }
}
