<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Package\StorePackageRequest;
use App\Http\Requests\Admin\Package\UpdatePackageRequest;
use App\Models\Package;
use App\Services\Admin\PackageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use RuntimeException;

class PackageController extends Controller
{
    public function __construct(private PackageService $packageService)
    {
    }

    public function store(StorePackageRequest $request): RedirectResponse
    {
        try {
            $payload = $request->payload();
            if ($request->hasFile('thumbnail')) {
                $payload['thumbnail'] = $request->file('thumbnail');
            }
            $this->packageService->createPackage($payload);
        } catch (RuntimeException $exception) {
            return back()
                ->withInput()
                ->withErrors(['general' => $exception->getMessage()]);
        }

        return redirect()
            ->route('admin.packages')
            ->with('status', 'Paket berhasil ditambahkan.');
    }

    public function update(UpdatePackageRequest $request, Package $package): RedirectResponse
    {
        try {
            $payload = $request->payload();
            if ($request->hasFile('thumbnail')) {
                $payload['thumbnail'] = $request->file('thumbnail');
            }
            $this->packageService->updatePackage($package, $payload);
        } catch (RuntimeException $exception) {
            return back()
                ->withInput()
                ->withErrors(['general' => $exception->getMessage()]);
        }

        return redirect()
            ->route('admin.packages')
            ->with('status', 'Paket berhasil diperbarui.');
    }

    public function destroy(Package $package): RedirectResponse
    {
        try {
            $this->packageService->deletePackage($package);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['general' => $exception->getMessage()]);
        }

        return redirect()
            ->route('admin.packages')
            ->with('status', 'Paket berhasil dihapus.');
    }
}
