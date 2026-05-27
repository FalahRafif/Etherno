<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Admin\ProfileService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private ProfileService $profileService
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
