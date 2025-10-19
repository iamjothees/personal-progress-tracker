<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TimerController;
use App\Models\Timer;
use Illuminate\Support\Facades\Route;


Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function(){
    Route::delete('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('profile', [AuthController::class, 'profile'])->name('profile');

    Route::prefix('timers')->name('timers')->group(function (){
        Route::get('/', [TimerController::class, 'index'])->name('')
            ->can('viewAny', Timer::class);
        Route::post('/actions/start', [TimerController::class, 'start'])->name('.start')
            ->can('act', Timer::class);
        Route::get('/{timer}', [TimerController::class, 'view'])->name('.view')
            ->can('view', 'timer');
        
        Route::prefix('{timer}/actions')->name('.actions')->group(function (){
            Route::post('/pause', [TimerController::class, 'pause'])->name('.resume');
            Route::post('{timerActivity}/resume', [TimerController::class, 'resume'])->name('.resume');
            Route::post('/stop', [TimerController::class, 'stop'])->name('.stop');
        })->can('act', 'timer');
    });
});

