<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
      public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'invalid_credentials',
            ], 401);
        }

        $user = $request->user();
        $role = $user->role ?? null;

        $token = $user->createToken('api-token')->plainTextToken;



        $response = response()->json([
            'success' => true,
            'message' => 'Logged in',
            'user'    => [
                'id'           => $user->id,
                'email'        => $user->email,
                'role'         => $role,
                'token'        => $token,

            ],
        ]);

        return $response->cookie(
            'auth_token', // cookie name
            $token,       // token value
            60 * 24,      // 1 day in minutes
            '/',          // path
            null,         // domain
            false,        // secure (set true in production HTTPS)
            true          // httpOnly
        );
    }
}
