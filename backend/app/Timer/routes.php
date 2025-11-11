<?php

use App\Timer\Http\Controllers\TimerController;
use App\Timer\Models\Timer;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function(){
    Route::prefix('timers')->name('timers.')->group(function (){
        Route::get('/', [TimerController::class, 'index'])->name('index')
            ->can('viewAny', Timer::class);
        Route::post('/actions/start', [TimerController::class, 'start'])->name('start')
            ->can('act', Timer::class);
        Route::get('/{timer}', [TimerController::class, 'view'])->name('view')
            ->can('view', 'timer');
        
        Route::prefix('{timer}/actions')->name('actions.')->group(function (){
            Route::post('/pause', [TimerController::class, 'pause'])->name('pause')->can('act', 'timer');
            Route::post('{timerActivity}/resume', [TimerController::class, 'resume'])->name('resume')->can('act', 'timer');
            Route::post('/stop', [TimerController::class, 'stop'])->name('stop')->can('act', 'timer');
            Route::post('/reset', [TimerController::class, 'reset'])->name('reset')->can('act', 'timer');
        });

        Route::post('/{timer}/time-trackables', [TimerController::class, 'addTimeTrackables'])->name('addTimeTrackables')
            ->can('addTimeTrackables', 'timer');
    });
});