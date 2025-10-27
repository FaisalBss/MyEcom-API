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
            $verificationLink = URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(60),
                ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
            );

            Mail::to($user->email)->queue(new SendVerificationEmail($user, $verificationLink));

        } catch (\Exception $e) {
            Log::error('Failed to send verification email for user ' . $user->id . ': ' . $e->getMessage());
        }
    }

    public function verifyEmail($id, $hash): array
    {
        $user = User::find($id);

        if (!$user || !hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return [
                'success' => false,
                'message' => 'Invalid verification link or user not found.',
                'status'  => 400
            ];
        }

        if ($user->hasVerifiedEmail()) {
            return [
                'success' => true,
                'message' => 'Email already verified.',
                'status'  => 200
            ];
        }

        $user->markEmailAsVerified();

        return [
            'success' => true,
            'message' => 'Email verified successfully!',
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
