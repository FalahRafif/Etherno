<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\Admin\DpPercentageRuleService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class DpPercentageRuleController extends Controller
{
    public function __construct(private DpPercentageRuleService $dpPercentageRuleService)
    {
    }

    public function index(Request $request): View
    {
        $payload = $this->dpPercentageRuleService->getPagePayload();

        return view('pages.admin.dp-percentage-rules.index', array_merge($payload, [
            'title' => 'Aturan Persen DP - Etherno Admin',
        ]));
    }

    public function create(): View
    {
        return view('pages.admin.dp-percentage-rules.create', array_merge(
            $this->dpPercentageRuleService->getFormPayload(),
            ['title' => 'Tambah Aturan Persen DP - Etherno Admin']
        ));
    }

    public function edit(Setting $setting): View
    {
        try {
            $managedRule = $this->dpPercentageRuleService->resolveEditableRule($setting);
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('admin.dp-percentage-rules')
                ->withErrors(['general' => $exception->getMessage()]);
        }

        return view('pages.admin.dp-percentage-rules.edit', array_merge(
            $this->dpPercentageRuleService->getFormPayload(),
            [
                'title' => 'Edit Aturan Persen DP - Etherno Admin',
                'managedRule' => $managedRule,
            ]
        ));
    }
}
