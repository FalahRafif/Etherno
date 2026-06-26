<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Services\Portal\GuestPackageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function __construct(private GuestPackageService $guestPackageService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $type = $request->query('type', 'wedding');
        $page = max(1, (int) $request->query('page', 1));
        $perPage = 6;

        $result = $this->guestPackageService->getPaginatedPackages($type, $page, $perPage);

        return response()->json($result);
    }
}
