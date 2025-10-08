<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\User;

class AuthManualController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if ($this->authService->login($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = auth()->user();

            if ($user->role == User::ROLE_ADMIN) {
                return redirect()->intended('/admin/dashboard');
            }

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'بيانات تسجيل الدخول غير صحيحة.',
        ]);
    }

    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = $this->authService->register(
            $request->only(['name', 'email', 'password'])
        );

        auth()->login($user);
        if ($user->role == User::ROLE_ADMIN) {
            return redirect('/admin/dashboard');
        }

        return redirect('/');
    }

    public function logout(): RedirectResponse
    {
        $this->authService->logout();
        return redirect('/login');
    }
}
