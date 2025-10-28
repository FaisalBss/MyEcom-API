<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|min:4|max:6',
        ]);

        $user = $request->user();

        $result = $this->authService->verifyEmailOtp($user, $request->otp);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message']
        ], $result['status']);
    }

    public function resendOtp(Request $request)
    {
        $user = $request->user();

        $result = $this->authService->resendVerificationEmail($user);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message']
        ], $result['status']);
    }
}
