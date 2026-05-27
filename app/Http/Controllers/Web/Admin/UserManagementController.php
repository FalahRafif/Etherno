<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserManagement\StoreUserRequest;
use App\Http\Requests\Admin\UserManagement\UpdateUserRequest;
use App\Models\User;
use App\Services\AuthService;
use App\Services\Admin\UserManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class UserManagementController extends Controller
{
    public function __construct(
        private UserManagementService $userManagementService,
        private AuthService $authService
    ) {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $payload = $this->userManagementService->getPagePayload($search);

        return view('pages.admin.management-user.index', array_merge($payload, [
            'title' => 'Management User/Akun - Etherno Admin',
            'search' => $search,
        ]));
    }

    public function create(): View
    {
        return view('pages.admin.management-user.create', [
            'title' => 'Tambah Akun Internal - Etherno Admin',
            'roles' => $this->userManagementService->availableRoles(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        try {
            $this->userManagementService->createUser($request->payload());
        } catch (RuntimeException $exception) {
            return back()
                ->withInput()
                ->withErrors(['general' => $exception->getMessage()]);
        }

        return redirect()
            ->route('admin.users')
            ->with('status', 'Akun internal berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        $managedUser = $this->userManagementService->resolveManageableUser($user);

        return view('pages.admin.management-user.edit', [
            'title' => 'Edit Akun Internal - Etherno Admin',
            'roles' => $this->userManagementService->availableRoles(),
            'managedUser' => $managedUser,
            'profileImageUrl' => $this->userManagementService->resolveProfileImageUrl($managedUser),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        try {
            $updatedUser = $this->userManagementService->updateUser($user, $request->payload());

            if (auth()->id() === (int) $updatedUser->getKey()) {
                $this->authService->syncInternalSession($request, $updatedUser);
            }
        } catch (RuntimeException $exception) {
            return back()
                ->withInput()
                ->withErrors(['general' => $exception->getMessage()]);
        }

        return redirect()
            ->route('admin.users')
            ->with('status', 'Data akun berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        try {
            $this->userManagementService->deleteUser($user);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['general' => $exception->getMessage()]);
        }

        return redirect()
            ->route('admin.users')
            ->with('status', 'Akun berhasil dihapus.');
    }
}
