<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Mail\SendVerificationEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

class AuthService
{
    public function login(array $credentials): ?string
    {
        return JWTAuth::attempt($credentials) ?: null;
    }

    public function register(array $data): array
    {
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'] ?? User::ROLE_USER,
        ]);

        $this->sendVerificationEmail($user);

        $token = JWTAuth::fromUser($user);

        return [
            'user'    => $user,
            'token'   => $token,
            'message' => __('User registered successfully. Please check your email for verification.')
        ];
    }

    protected function sendVerificationEmail(User $user): void
    {
        try {
            $otp = rand(1000, 9999);

            $user->verification_code = $otp;
            $user->verification_code_expires_at = Carbon::now()->addMinutes(10);
            $user->save();

            Mail::to($user->email)->queue(new SendVerificationEmail($user, $otp));

        } catch (\Exception $e) {
            Log::error('Failed to queue verification email for user ' . $user->id . ': ' . $e->getMessage());
        }
    }

   public function verifyEmailOtp(User $user, string $otp): array
    {
        if ($user->hasVerifiedEmail()) {
            return [
                'success' => true,
                'message' => 'Email already verified.',
                'status'  => 200
            ];
        }

        if ($user->verification_code !== $otp) {
             return [
                'success' => false,
                'message' => 'Invalid verification code.',
                'status'  => 400
            ];
        }

        if (Carbon::now()->isAfter($user->verification_code_expires_at)) {
             return [
                'success' => false,
                'message' => 'Verification code has expired.',
                'status'  => 400
            ];
        }

        $user->email_verified_at = Carbon::now();
        $user->verification_code = null;
        $user->verification_code_expires_at = null;
        $user->save();

        return [
            'success' => true,
            'message' => 'Email verified successfully!',
            'status'  => 200
        ];
    }

    public function resendVerificationEmail(User $user): array
    {
        if ($user->hasVerifiedEmail()) {
            return [
                'success' => false,
                'message' => 'Email is already verified.',
                'status'  => 400
            ];
        }

        $this->sendVerificationEmail($user);

        return [
            'success' => true,
            'message' => 'A new verification code has been sent to your email.',
            'status'  => 200
        ];
    }


    public function logout(): void
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }

    public function refresh(): string
    {
        return JWTAuth::refresh(JWTAuth::getToken());
    }

    public function me(): ?User
    {
        return auth('api')->user();
    }
}
