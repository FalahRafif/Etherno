<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function attempt(string $email, string $password, bool $remember = false): bool
    {
        return Auth::attempt(['email' => $email, 'password' => $password], $remember);
    }

    public function logout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
    }

    public function isInternalUser(?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        $internalRoles = config('role_access.roles.internal', ['Admin', 'Petugas']);

        return $user->hasRole($internalRoles);
    }

    public function resolveDashboardRoute(?User $user): ?string
    {
        if ($user === null) {
            return null;
        }

        $dashboardByRole = config('role_access.dashboard_route_by_role', []);
        $roleName = $user->roleName();

        if (!is_string($roleName)) {
            return null;
        }

        return $dashboardByRole[$roleName] ?? null;
    }

    public function syncRoleSession(Request $request, User $user): void
    {
        $roleName = $user->roleName();
        $prefixByRole = config('role_access.route_prefix_by_role', []);
        $routePrefix = is_string($roleName) ? ($prefixByRole[$roleName] ?? null) : null;

        if ($routePrefix === null) {
            return;
        }

        $request->session()->put('auth.role', $routePrefix);
    }

    public function clearRoleSession(Request $request): void
    {
        $request->session()->forget('auth.role');
    }
}
