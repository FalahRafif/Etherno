<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\BookingCalendarService;
use App\Services\Admin\BookingDetailService;
use App\Services\Admin\BookingListService;
use App\Services\Portal\InternalPageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPreviewController extends Controller
{
    public function __construct(
        private InternalPageService $internalPageService,
        private BookingListService $bookingListService,
        private BookingDetailService $bookingDetailService,
        private BookingCalendarService $bookingCalendarService
    ) {
    }

    public function dashboard(): View
    {
        $payload = $this->bookingListService->getDashboardPayload();

        return $this->internalPageService->render($this->resolvePanelPrefix(), 'dashboard', $payload);
    }

    public function bookingsList(Request $request): View
    {
        $payload = $this->bookingListService->getPagePayload($request->all());

        return $this->internalPageService->render($this->resolvePanelPrefix(), 'bookings.list', $payload);
    }

    public function bookingDetail(string $booking): View
    {
        $payload = $this->bookingDetailService->getPagePayload(strtoupper($booking));

        $prefix = $this->resolvePanelPrefix();

        if (!$payload['booking']) {
            return $this->internalPageService->render($prefix, 'bookings.detail', [
                'bookingCode' => strtoupper($booking),
                'booking' => null,
            ]);
        }

        return $this->internalPageService->render($prefix, 'bookings.detail', $payload);
    }

    public function calendar(Request $request): View
    {
        $payload = $this->bookingCalendarService->getPagePayload($request->all());

        return $this->internalPageService->render($this->resolvePanelPrefix(), 'calendar', $payload);
    }

    public function calendarEvents(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'start' => ['nullable', 'date'],
            'end' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'max:120'],
            'date_start' => ['nullable', 'date'],
            'date_end' => ['nullable', 'date'],
        ]);

        $events = $this->bookingCalendarService->getCalendarEvents($payload);

        return response()->json([
            'events' => $events,
        ]);
    }

    public function packages(): View
    {
        return $this->internalPageService->render($this->resolvePanelPrefix(), 'packages');
    }

    public function customers(Request $request): View
    {
        $payload = $this->bookingListService->getCustomersPayload($request->all());

        return $this->internalPageService->render($this->resolvePanelPrefix(), 'customers', $payload);
    }

    public function settings(): View
    {
        $payload = $this->bookingListService->getSettingsPayload();

        return $this->internalPageService->render($this->resolvePanelPrefix(), 'settings', $payload);
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

