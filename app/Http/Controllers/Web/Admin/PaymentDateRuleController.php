<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\Admin\PaymentDateRuleService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class PaymentDateRuleController extends Controller
{
    public function __construct(private PaymentDateRuleService $paymentDateRuleService)
    {
    }

    public function index(Request $request): View
    {
        $payload = $this->paymentDateRuleService->getPagePayload();

        return view('pages.admin.payment-date-rules.index', array_merge($payload, [
            'title' => 'Aturan Waktu Pembayaran - Etherno Admin',
            'resolveValueNote' => fn (string $value): string => $this->paymentDateRuleService->resolveValueNote($value),
        ]));
    }

    public function create(): View
    {
        return view('pages.admin.payment-date-rules.create', [
            'title' => 'Tambah Aturan Waktu Pembayaran - Etherno Admin',
        ]);
    }

    public function edit(Setting $setting): View
    {
        try {
            $managedRule = $this->paymentDateRuleService->resolveEditableRule($setting);
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('admin.payment-date-rules')
                ->withErrors(['general' => $exception->getMessage()]);
        }

        return view('pages.admin.payment-date-rules.edit', [
            'title' => 'Edit Aturan Waktu Pembayaran - Etherno Admin',
            'managedRule' => $managedRule,
        ]);
    }
}
