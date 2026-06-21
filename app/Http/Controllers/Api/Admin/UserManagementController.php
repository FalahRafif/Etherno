<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserManagement\StoreUserRequest;
use App\Http\Requests\Admin\UserManagement\UpdateUserRequest;
use App\Models\User;
use App\Services\Admin\UserManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

class UserManagementController extends Controller
{
    public function __construct(private UserManagementService $userManagementService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $payload = $this->userManagementService->getUsersForApi((string) $request->query('search', ''));

        return response()->json([
            'message' => 'Data akun berhasil dimuat.',
            'data' => $payload,
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
            ->with('status', 'Akun berhasil ditambahkan.');
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        try {
            $this->userManagementService->updateUser($user, $request->payload());
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
