<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService)
    {
    }

    public function showLogin(Request $request): View|RedirectResponse
    {
        if (auth()->check()) {
            $user = $request->user()?->loadMissing('role');

            if ($user instanceof User && $this->authService->isInternalUser($user)) {
                $dashboardRoute = $this->authService->resolveDashboardRoute($user);

                if (is_string($dashboardRoute)) {
                    return redirect()->route($dashboardRoute);
                }
            }

            $this->authService->clearRoleSession($request);
            $this->authService->logout();
        }

        return view('pages.auth.login', ['title' => 'Login - Etherno Admin']);
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        if ($this->authService->attempt($request->email(), $request->password(), $request->remember())) {
            $request->session()->regenerate();
            $user = $request->user()?->loadMissing('role');

            if (!$user instanceof User || !$this->authService->isInternalUser($user)) {
                $this->authService->clearRoleSession($request);
                $this->authService->logout();

                return back()
                    ->withErrors(['email' => 'Akun ini belum memiliki akses ke panel internal.'])
                    ->withInput($request->only('email'));
            }

            $this->authService->syncRoleSession($request, $user);
            $dashboardRoute = $this->authService->resolveDashboardRoute($user) ?? 'admin.dashboard';

            return redirect()->route($dashboardRoute);
        }

        return back()
            ->withErrors(['email' => 'Email atau password salah.'])
            ->withInput($request->only('email'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->authService->clearRoleSession($request);
        $this->authService->logout();

        return redirect()->route('login');
    }
}
