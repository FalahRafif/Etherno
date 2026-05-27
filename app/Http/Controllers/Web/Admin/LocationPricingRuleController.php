<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\LocationPricingRule;
use App\Services\Admin\LocationPricingRuleService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class LocationPricingRuleController extends Controller
{
    public function __construct(private LocationPricingRuleService $locationPricingRuleService)
    {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $payload = $this->locationPricingRuleService->getPagePayload($search);

        return view('pages.admin.location-pricing-rules.index', array_merge($payload, [
            'title' => 'Aturan Harga Lokasi - Etherno Admin',
            'search' => $search,
        ]));
    }

    public function create(): View
    {
        return view('pages.admin.location-pricing-rules.create', array_merge(
            $this->locationPricingRuleService->getFormPayload(),
            ['title' => 'Tambah Aturan Harga Lokasi - Etherno Admin']
        ));
    }

    public function edit(LocationPricingRule $locationPricingRule): View
    {
        try {
            $managedRule = $this->locationPricingRuleService->resolveEditableRule($locationPricingRule);
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('admin.location.rules')
                ->withErrors(['general' => $exception->getMessage()]);
        }

        return view('pages.admin.location-pricing-rules.edit', array_merge(
            $this->locationPricingRuleService->getFormPayload($managedRule),
            [
                'title' => 'Edit Aturan Harga Lokasi - Etherno Admin',
                'managedRule' => $managedRule,
            ]
        ));
    }
}
