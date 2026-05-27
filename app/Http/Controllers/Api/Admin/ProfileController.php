<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Profile\UpdateProfileRequest;
use App\Models\User;
use App\Services\Admin\ProfileService;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

class ProfileController extends Controller
{
    public function __construct(
        private ProfileService $profileService,
        private AuthService $authService
    ) {
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
}
