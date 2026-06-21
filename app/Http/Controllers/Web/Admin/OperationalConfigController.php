<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\Admin\OperationalConfigService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class OperationalConfigController extends Controller
{
    public function __construct(private OperationalConfigService $operationalConfigService)
    {
    }

    public function index(Request $request): View
    {
        $payload = $this->operationalConfigService->getPagePayload();

        return view('pages.admin.operational-config.index', array_merge($payload, [
            'title' => 'Aturan Aplikasi - Etherno Admin',
        ]));
    }

    public function create(): View
    {
        return view('pages.admin.operational-config.create', [
            'title' => 'Tambah Aturan Aplikasi - Etherno Admin',
        ]);
    }

    public function edit(Setting $setting): View
    {
        try {
            $managedRule = $this->operationalConfigService->resolveEditableRule($setting);
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('admin.operational-config')
                ->withErrors(['general' => $exception->getMessage()]);
        }

        return view('pages.admin.operational-config.edit', [
            'title' => 'Edit Aturan Aplikasi - Etherno Admin',
            'managedRule' => $managedRule,
        ]);
    }
}
