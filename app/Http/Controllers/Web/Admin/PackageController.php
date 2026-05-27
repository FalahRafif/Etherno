<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Package\StorePackageRequest;
use App\Http\Requests\Admin\Package\UpdatePackageRequest;
use App\Models\Package;
use App\Services\Admin\PackageService;
use Illuminate\Http\RedirectResponse;
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

    public function edit(Package $package): View|RedirectResponse
    {
        try {
            $managedPackage = $this->packageService->resolveEditablePackage($package);
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('admin.packages')
                ->withErrors(['general' => $exception->getMessage()]);
        }

        return view('pages.admin.management-package.edit', array_merge(
            $this->packageService->getFormPayload(),
            [
                'title' => 'Edit Paket - Etherno Admin',
                'managedPackage' => $managedPackage,
            ]
        ));
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
