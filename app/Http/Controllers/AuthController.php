<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterAuthRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(RegisterAuthRequest $request)
    {
        $data = $request->validated();
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $token = $user->createToken('main')->plainTextToken;

        return response([
            'user'  => $user,
            'token' => $token,
        ]);
    }

    public function login(Request $request)
    {
       $credentials = $request->validate([
           'email' => 'required|email|string|exists:users,email',
           'password' => [
               'required',
           ],
           'remember' => 'boolean'
       ]);

       $remember = $credentials['remember'] ?? false;
       unset($credentials['remember']);

       if (!Auth::attempt($credentials, $remember)) {
           return response([
               'error' => 'Дані для входу не коректні'
           ], 422);
       }

       $user = Auth::user();
       $token = $user->createToken( 'main')->plainTextToken;

       return response([
           'user' => $user,
           'token' => $token,
       ]);
    }

    public function logout()
    {
        /** @var User $user */
        $user = Auth::user();

        $user->currentAccessToken()->delete();

        return response([
            'success' => true
        ]);



    }
}
