<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Admin\UserManagementService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function __construct(
        private UserManagementService $userManagementService
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
}
