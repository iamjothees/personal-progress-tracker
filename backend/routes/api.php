<?php

use App\Http\Controllers\AuthController;
use App\Http\Responses\ApiResponse;
use Illuminate\Support\Facades\Route;


Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function(){
    Route::delete('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('profile', [AuthController::class, 'profile'])->name('profile');
});

Route::get('test', fn () => new ApiResponse(['message' => 'Api running successfully']) )->name('api-test');
