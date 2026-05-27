<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Services\Admin\PackageService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class PackageController extends Controller
{
    public function __construct(private PackageService $packageService)
    {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $payload = $this->packageService->getPagePayload($search);

        return view('pages.admin.management-package.index', array_merge($payload, [
            'title' => 'Management Paket - Etherno Admin',
            'search' => $search,
        ]));
    }

    public function create(): View
    {
        return view('pages.admin.management-package.create', array_merge(
            $this->packageService->getFormPayload(),
            ['title' => 'Tambah Paket - Etherno Admin']
        ));
    }

    public function edit(Package $package): View
    {
        $managedPackage = $this->packageService->resolveEditablePackage($package);

        return view('pages.admin.management-package.edit', array_merge(
            $this->packageService->getFormPayload(),
            [
                'title' => 'Edit Paket - Etherno Admin',
                'managedPackage' => $managedPackage,
            ]
        ));
    }
}
