<?php

use App\Task\Http\Controllers\TaskController;
use App\Task\Models\Task;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function(){
    Route::prefix('tasks')->name('tasks')->group(function(){
        Route::get('/', [TaskController::class, 'index'])->name('.index')
            ->can('viewAny', Task::class);
        Route::post('/', [TaskController::class, 'store'])->name('.store')
            ->can('create', Task::class);
        Route::get('/{task}', [TaskController::class, 'show'])->name('.show')
            ->can('update', 'task');
        Route::put('/{task}', [TaskController::class, 'update'])->name('.update')
            ->can('update', 'task');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name('.destroy')
            ->can('delete', 'task');
    });
});

