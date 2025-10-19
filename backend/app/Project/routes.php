<?php

use App\Project\Http\Controllers\ProjectController;
use App\Project\Models\Project;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function(){
    Route::prefix('projects')->name('projects')->group(function(){
        Route::get('/', [ProjectController::class, 'index'])->name('.index')
            ->can('viewAny', Project::class);
        Route::post('/', [ProjectController::class, 'store'])->name('.store')
            ->can('create', Project::class);
        Route::get('/{project}', [ProjectController::class, 'show'])->name('.show')
            ->can('view', 'project');
        Route::put('/{project}', [ProjectController::class, 'update'])->name('.update')
            ->can('update', 'project');
        Route::delete('/{project}', [ProjectController::class, 'destroy'])->name('.destroy')
            ->can('delete', 'project');
    });
});