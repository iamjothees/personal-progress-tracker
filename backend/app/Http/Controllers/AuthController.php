<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Responses\ApiResponse;
use App\Models\User;

class AuthController extends Controller
{
    public function register(RegisterRequest $request){
        $user = User::create($request->validated());
        return new ApiResponse(data: ['user'=> $user], httpCode: 201);
    }

    public function login(LoginRequest $request){
        $credentials = $request->validated();
        if (!auth()->attempt($credentials)) {
            return new ApiResponse(data: ['message' => 'Invalid credentials'], httpCode: 401);
        }
        $token = auth()->user()->createToken('authToken')->plainTextToken;
        return new ApiResponse(data: ['token' => $token]);
    }

    public function profile(){
        return new ApiResponse(data: ['user' => auth()->user()]);
    }

    public function logout(){
        auth()->logout();
        return new ApiResponse(data: ['success' => true]);
    }
}
