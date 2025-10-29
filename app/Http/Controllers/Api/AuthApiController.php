<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthApiController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $token = $this->authService->login($credentials);

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid login'
            ], 401);
        }

        return response()->json([
            'success'    => true,
            'message'    => 'Login successful',
            'user'       => auth('api')->user(),
            'token'      => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $result = $this->authService->register(
            $request->only(['name', 'email', 'password', 'role'])
        );

        return response()->json([
            'success'    => true,
            'message'    => $result['message'],
            'user'       => $result['user'],
            'token'      => $result['token'],
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ], 201);
    }

    public function logout()
    {
        $this->authService->logout();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    public function getProfileInfo()
    {
        return response()->json([
            'success' => true,
            'user'    => auth('api')->user()
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'success'    => true,
            'token'      => $this->authService->refresh(),
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }
}
