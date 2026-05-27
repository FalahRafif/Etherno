<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DpPercentageRule\StoreDpPercentageRuleRequest;
use App\Http\Requests\Admin\DpPercentageRule\UpdateDpPercentageRuleRequest;
use App\Models\Setting;
use App\Services\Admin\DpPercentageRuleService;
use Illuminate\Http\RedirectResponse;
use RuntimeException;

class DpPercentageRuleController extends Controller
{
    public function __construct(private DpPercentageRuleService $dpPercentageRuleService)
    {
    }

    public function store(StoreDpPercentageRuleRequest $request): RedirectResponse
    {
        try {
            $this->dpPercentageRuleService->createRule($request->payload());
        } catch (RuntimeException $exception) {
            return back()
                ->withInput()
                ->withErrors(['general' => $exception->getMessage()]);
        }

        return redirect()
            ->route('admin.dp-percentage-rules')
            ->with('status', 'Aturan persen DP berhasil ditambahkan.');
    }

    public function update(UpdateDpPercentageRuleRequest $request, Setting $setting): RedirectResponse
    {
        try {
            $this->dpPercentageRuleService->updateRule($setting, $request->payload());
        } catch (RuntimeException $exception) {
            return back()
                ->withInput()
                ->withErrors(['general' => $exception->getMessage()]);
        }

        return redirect()
            ->route('admin.dp-percentage-rules')
            ->with('status', 'Aturan persen DP berhasil diperbarui.');
    }

    public function destroy(Setting $setting): RedirectResponse
    {
        try {
            $this->dpPercentageRuleService->deleteRule($setting);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['general' => $exception->getMessage()]);
        }

        return redirect()
            ->route('admin.dp-percentage-rules')
            ->with('status', 'Aturan persen DP berhasil dihapus.');
    }
}
