<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentDateRule\StorePaymentDateRuleRequest;
use App\Http\Requests\Admin\PaymentDateRule\UpdatePaymentDateRuleRequest;
use App\Models\Setting;
use App\Services\Admin\PaymentDateRuleService;
use Illuminate\Http\RedirectResponse;
use RuntimeException;

class PaymentDateRuleController extends Controller
{
    public function __construct(private PaymentDateRuleService $paymentDateRuleService)
    {
    }

    public function store(StorePaymentDateRuleRequest $request): RedirectResponse
    {
        try {
            $this->paymentDateRuleService->createRule($request->payload());
        } catch (RuntimeException $exception) {
            return back()
                ->withInput()
                ->withErrors(['general' => $exception->getMessage()]);
        }

        return redirect()
            ->route('admin.payment-date-rules')
            ->with('status', 'Aturan waktu pembayaran berhasil ditambahkan.');
    }

    public function update(UpdatePaymentDateRuleRequest $request, Setting $setting): RedirectResponse
    {
        try {
            $this->paymentDateRuleService->updateRule($setting, $request->payload());
        } catch (RuntimeException $exception) {
            return back()
                ->withInput()
                ->withErrors(['general' => $exception->getMessage()]);
        }

        return redirect()
            ->route('admin.payment-date-rules')
            ->with('status', 'Aturan waktu pembayaran berhasil diperbarui.');
    }

    public function destroy(Setting $setting): RedirectResponse
    {
        try {
            $this->paymentDateRuleService->deleteRule($setting);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['general' => $exception->getMessage()]);
        }

        return redirect()
            ->route('admin.payment-date-rules')
            ->with('status', 'Aturan waktu pembayaran berhasil dihapus.');
    }
}
