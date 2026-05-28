<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\BookingListService;
use App\Services\Portal\InternalPageService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPreviewController extends Controller
{
    public function __construct(
        private InternalPageService $internalPageService,
        private BookingListService $bookingListService
    )
    {
    }

    public function dashboard(): View
    {
        return $this->internalPageService->render($this->resolvePanelPrefix(), 'dashboard');
    }

    public function bookingRequests(): View
    {
        return $this->internalPageService->render($this->resolvePanelPrefix(), 'bookings.requests');
    }

    public function bookingsList(Request $request): View
    {
        $payload = $this->bookingListService->getPagePayload($request->all());

        return $this->internalPageService->render($this->resolvePanelPrefix(), 'bookings.list', $payload);
    }

    public function bookingsActive(): View
    {
        return $this->internalPageService->render($this->resolvePanelPrefix(), 'bookings.active');
    }

    public function bookingDetail(string $booking): View
    {
        return $this->internalPageService->render($this->resolvePanelPrefix(), 'bookings.detail', [
            'bookingCode' => strtoupper($booking),
        ]);
    }

    public function calendar(): View
    {
        return $this->internalPageService->render($this->resolvePanelPrefix(), 'calendar');
    }

    public function dpVerification(): View
    {
        return $this->internalPageService->render($this->resolvePanelPrefix(), 'payments.dp');
    }

    public function finalPayment(): View
    {
        return $this->internalPageService->render($this->resolvePanelPrefix(), 'payments.final');
    }

    public function pricingReviews(): View
    {
        return $this->internalPageService->render($this->resolvePanelPrefix(), 'pricing.reviews');
    }

    public function packages(): View
    {
        return $this->internalPageService->render($this->resolvePanelPrefix(), 'packages');
    }

    public function reschedules(): View
    {
        return $this->internalPageService->render($this->resolvePanelPrefix(), 'reschedules');
    }

    public function cancellations(): View
    {
        return $this->internalPageService->render($this->resolvePanelPrefix(), 'cancellations');
    }

    public function forceMajeure(): View
    {
        return $this->internalPageService->render($this->resolvePanelPrefix(), 'force.majeure');
    }

    public function customers(): View
    {
        return $this->internalPageService->render($this->resolvePanelPrefix(), 'customers');
    }

    public function settings(): View
    {
        return $this->internalPageService->render($this->resolvePanelPrefix(), 'settings');
    }

    private function resolvePanelPrefix(): string
    {
        $routeName = request()->route()?->getName();

        if (is_string($routeName) && str_contains($routeName, '.')) {
            return explode('.', $routeName)[0];
        }

        return 'admin';
    }
}
