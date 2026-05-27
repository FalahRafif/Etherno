<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Profile\UpdateProfileRequest;
use App\Models\User;
use App\Services\Admin\ProfileService;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class ProfileController extends Controller
{
    public function __construct(
        private ProfileService $profileService,
        private AuthService $authService
    ) {
    }

    public function edit(Request $request): View
    {
        $user = $request->user();

        if (!$user instanceof User) {
            abort(403);
        }

        $user->loadMissing(['role', 'profileImageAttachment']);

        return view('pages.admin.profile.edit', [
            'title' => $this->resolvePageTitle($request),
            'profileUser' => $user,
            'profileImageUrl' => $this->profileService->resolveProfileImageUrl($user),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();

        if (!$user instanceof User) {
            abort(403);
        }

        try {
            $updatedUser = $this->profileService->updateProfile($user, $request->payload());
            $this->authService->syncInternalSession($request, $updatedUser);
        } catch (RuntimeException $exception) {
            return back()
                ->withInput()
                ->withErrors(['general' => $exception->getMessage()]);
        }

        return back()->with('status', 'Profil berhasil diperbarui.');
    }

    private function resolvePageTitle(Request $request): string
    {
        $routeName = $request->route()?->getName();
        $panelPrefix = 'admin';

        if (is_string($routeName) && str_contains($routeName, '.')) {
            $panelPrefix = explode('.', $routeName)[0];
        }

        $panelName = config("role_access.panel_title_by_prefix.{$panelPrefix}", ucfirst($panelPrefix));

        return "Profil Saya - Etherno {$panelName}";
    }
}
