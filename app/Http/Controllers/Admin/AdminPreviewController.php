<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Portal\InternalPageService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPreviewController extends Controller
{
    public function __construct(private InternalPageService $internalPageService)
    {
    }

    public function dashboard(Request $request): View
    {
        return $this->internalPageService->render($this->resolvePanelPrefix($request), 'dashboard');
    }

    public function bookingRequests(Request $request): View
    {
        return $this->internalPageService->render($this->resolvePanelPrefix($request), 'bookings.requests');
    }

    public function bookingsActive(Request $request): View
    {
        return $this->internalPageService->render($this->resolvePanelPrefix($request), 'bookings.active');
    }

    public function bookingDetail(Request $request, string $booking): View
    {
        return $this->internalPageService->render($this->resolvePanelPrefix($request), 'bookings.detail', [
            'bookingCode' => strtoupper($booking),
        ]);
    }

    public function calendar(Request $request): View
    {
        return $this->internalPageService->render($this->resolvePanelPrefix($request), 'calendar');
    }

    public function dpVerification(Request $request): View
    {
        return $this->internalPageService->render($this->resolvePanelPrefix($request), 'payments.dp');
    }

    public function finalPayment(Request $request): View
    {
        return $this->internalPageService->render($this->resolvePanelPrefix($request), 'payments.final');
    }

    public function pricingReviews(Request $request): View
    {
        return $this->internalPageService->render($this->resolvePanelPrefix($request), 'pricing.reviews');
    }

    public function packages(Request $request): View
    {
        return $this->internalPageService->render($this->resolvePanelPrefix($request), 'packages');
    }

    public function locationRules(Request $request): View
    {
        return $this->internalPageService->render($this->resolvePanelPrefix($request), 'location.rules');
    }

    public function reschedules(Request $request): View
    {
        return $this->internalPageService->render($this->resolvePanelPrefix($request), 'reschedules');
    }

    public function cancellations(Request $request): View
    {
        return $this->internalPageService->render($this->resolvePanelPrefix($request), 'cancellations');
    }

    public function forceMajeure(Request $request): View
    {
        return $this->internalPageService->render($this->resolvePanelPrefix($request), 'force.majeure');
    }

    public function customers(Request $request): View
    {
        return $this->internalPageService->render($this->resolvePanelPrefix($request), 'customers');
    }

    public function settings(Request $request): View
    {
        return $this->internalPageService->render($this->resolvePanelPrefix($request), 'settings');
    }

    private function resolvePanelPrefix(Request $request): string
    {
        $routeName = $request->route()?->getName();

        if (is_string($routeName) && str_contains($routeName, '.')) {
            return explode('.', $routeName)[0];
        }

        return 'admin';
    }
}
