<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;

class AuthController extends Controller
{
    public function register(RegisterRequest $request){
        $user = User::create($request->validated());
        return response()->json($user, 201);
    }

    public function login(LoginRequest $request){
        $credentials = $request->validated();
        if (!auth()->attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        $token = auth()->user()->createToken('authToken')->plainTextToken;
        return response()->json(['token' => $token]);
    }

    public function logout(){
        auth()->logout();
        return response()->json(true);
    }
}
