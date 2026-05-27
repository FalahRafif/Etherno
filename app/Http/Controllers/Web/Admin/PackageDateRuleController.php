<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\Admin\PackageDateRuleService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class PackageDateRuleController extends Controller
{
    public function __construct(private PackageDateRuleService $packageDateRuleService)
    {
    }

    public function index(Request $request): View
    {
        $payload = $this->packageDateRuleService->getPagePayload();

        return view('pages.admin.package-date-rules.index', array_merge($payload, [
            'title' => 'Aturan Waktu Paket - Etherno Admin',
            'resolveValueNote' => fn (string $value): string => $this->packageDateRuleService->resolveValueNote($value),
        ]));
    }

    public function create(): View
    {
        return view('pages.admin.package-date-rules.create', [
            'title' => 'Tambah Aturan Waktu Paket - Etherno Admin',
        ]);
    }

    public function edit(Setting $setting): View
    {
        try {
            $managedRule = $this->packageDateRuleService->resolveEditableRule($setting);
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('admin.package-date-rules')
                ->withErrors(['general' => $exception->getMessage()]);
        }

        return view('pages.admin.package-date-rules.edit', [
            'title' => 'Edit Aturan Waktu Paket - Etherno Admin',
            'managedRule' => $managedRule,
        ]);
    }
}
