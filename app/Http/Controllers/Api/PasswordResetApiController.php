<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Services\PasswordResetService;

class PasswordResetApiController extends Controller
{
    protected $service;

    public function __construct(PasswordResetService $service)
    {
        $this->service = $service;
    }

    public function sendResetLink(ForgotPasswordRequest $request)
    {
        $token = $this->service->createToken($request->email);

        $link = url('/reset-password/' . $token . '?email=' . $request->email);

        return response()->json([
            'success' => true,
            'message' => 'Reset link generated successfully.',
            'reset_link' => $link
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        if (!$this->service->validateToken($request->email)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired token.'
            ], 400);
        }

        $this->service->resetPassword($request->email, $request->password);

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully.'
        ]);
    }
}
