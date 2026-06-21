<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OperationalConfig\StoreOperationalConfigRequest;
use App\Http\Requests\Admin\OperationalConfig\UpdateOperationalConfigRequest;
use App\Models\Setting;
use App\Services\Admin\OperationalConfigService;
use Illuminate\Http\RedirectResponse;
use RuntimeException;

class OperationalConfigController extends Controller
{
    public function __construct(private OperationalConfigService $operationalConfigService)
    {
    }

    public function store(StoreOperationalConfigRequest $request): RedirectResponse
    {
        try {
            $this->operationalConfigService->createRule($request->payload());
        } catch (RuntimeException $exception) {
            return back()
                ->withInput()
                ->withErrors(['general' => $exception->getMessage()]);
        }

        return redirect()
            ->route('admin.operational-config')
            ->with('status', 'Konfigurasi aplikasi berhasil ditambahkan.');
    }

    public function update(UpdateOperationalConfigRequest $request, Setting $setting): RedirectResponse
    {
        try {
            $this->operationalConfigService->updateRule($setting, $request->payload());
        } catch (RuntimeException $exception) {
            return back()
                ->withInput()
                ->withErrors(['general' => $exception->getMessage()]);
        }

        return redirect()
            ->route('admin.operational-config')
            ->with('status', 'Konfigurasi aplikasi berhasil diperbarui.');
    }

    public function destroy(Setting $setting): RedirectResponse
    {
        try {
            $this->operationalConfigService->deleteRule($setting);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['general' => $exception->getMessage()]);
        }

        return redirect()
            ->route('admin.operational-config')
            ->with('status', 'Konfigurasi aplikasi berhasil dihapus.');
    }
}
