<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function register(Request $request){
        $user = User::create([
            'name' => $request->json('name'),
            'email' => $request->json('email'),
            'password' => bcrypt($request->json('password')),
        ]);
        return response()->json([
            'message' => 'success',
            'user' => $user
        ]);
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = $request->user();
            $token = $user->createToken('Gif user')->accessToken;
            return response()->json([
                'message' => 'success',
                'access_token' => $token,
                'user_id' => $user->id
            ]);
        }

        return response()->json([
            'message' => 'error user not found'
        ]);
    }
}
