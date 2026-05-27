<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PackageDateRule\StorePackageDateRuleRequest;
use App\Http\Requests\Admin\PackageDateRule\UpdatePackageDateRuleRequest;
use App\Models\Setting;
use App\Services\Admin\PackageDateRuleService;
use Illuminate\Http\RedirectResponse;
use RuntimeException;

class PackageDateRuleController extends Controller
{
    public function __construct(private PackageDateRuleService $packageDateRuleService)
    {
    }

    public function store(StorePackageDateRuleRequest $request): RedirectResponse
    {
        try {
            $this->packageDateRuleService->createRule($request->payload());
        } catch (RuntimeException $exception) {
            return back()
                ->withInput()
                ->withErrors(['general' => $exception->getMessage()]);
        }

        return redirect()
            ->route('admin.package-date-rules')
            ->with('status', 'Aturan waktu paket berhasil ditambahkan.');
    }

    public function update(UpdatePackageDateRuleRequest $request, Setting $setting): RedirectResponse
    {
        try {
            $this->packageDateRuleService->updateRule($setting, $request->payload());
        } catch (RuntimeException $exception) {
            return back()
                ->withInput()
                ->withErrors(['general' => $exception->getMessage()]);
        }

        return redirect()
            ->route('admin.package-date-rules')
            ->with('status', 'Aturan waktu paket berhasil diperbarui.');
    }

    public function destroy(Setting $setting): RedirectResponse
    {
        try {
            $this->packageDateRuleService->deleteRule($setting);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['general' => $exception->getMessage()]);
        }

        return redirect()
            ->route('admin.package-date-rules')
            ->with('status', 'Aturan waktu paket berhasil dihapus.');
    }
}
