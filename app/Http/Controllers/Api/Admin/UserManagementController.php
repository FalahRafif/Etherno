<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserManagement\StoreUserRequest;
use App\Http\Requests\Admin\UserManagement\UpdateUserRequest;
use App\Models\User;
use App\Services\Admin\UserManagementService;
use Illuminate\Http\JsonResponse;
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

    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            $user = $this->userManagementService->createUser($request->payload());
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Akun berhasil ditambahkan.',
            'data' => [
                'user' => $this->userManagementService->transformUsers(collect([$user]))[0],
            ],
        ], 201);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        try {
            $updatedUser = $this->userManagementService->updateUser($user, $request->payload());
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Data akun berhasil diperbarui.',
            'data' => [
                'user' => $this->userManagementService->transformUsers(collect([$updatedUser]))[0],
            ],
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        try {
            $this->userManagementService->deleteUser($user);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Akun berhasil dihapus.',
        ]);
    }
}
