<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JWTAuth;

class AuthApiController extends Controller
{
    public function register(Request $request)
    {
        $user = new \App\User;
        $user->email = $request->email;
        $user->name = $request->name;
        $user->password = bcrypt($request->password);
        $user->save();
        return response([
            'status' => 'success',
            'data' => $user
        ], 200);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if ( ! $token = JWTAuth::attempt($credentials)) {

            return response([
                'status' => 'error',
                'error' => 'invalid.credentials',
                'message' => 'Invalid Credentials.'
            ], 400);
        }
        return response([
            'status' => 'success'
        ])
            ->header('Authorization', $token);
    }

    public function user(Request $request)
    {
        $user = \App\User::find(\Auth::user()->id);
        return response([
            'status' => 'success',
            'data' => $user
        ]);
    }

    public function refresh()
    {
        return response([
            'status' => 'success'
        ]);
    }

    public function logout()
    {
        JWTAuth::invalidate();
        return response([
            'status' => 'success',
            'message' => 'Logged out Successfully.'
        ], 200);
    }
}