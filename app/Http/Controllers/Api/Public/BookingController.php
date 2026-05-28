<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\Booking\StoreBookingRequest;
use App\Services\Portal\GuestBookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use RuntimeException;

class BookingController extends Controller
{
    public function __construct(private GuestBookingService $guestBookingService)
    {
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        try {
            $booking = $this->guestBookingService->createBookingRequest($request->payload());
        } catch (RuntimeException $exception) {
            return back()
                ->withInput()
                ->withErrors(['general' => $exception->getMessage()]);
        }

        $customerName = trim(implode(' ', array_filter([
            $booking->customer?->first_name,
            $booking->customer?->last_name,
        ])));

        return redirect()
            ->route('booking.success')
            ->with([
                'booking_request_code' => $this->guestBookingService->buildRequestCode($booking),
                'booking_uuid' => $booking->uuid,
                'booking_customer_name' => $customerName !== '' ? $customerName : 'Customer',
            ]);
    }

    public function locationOptions(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'level' => ['required', 'string', Rule::in(['LL_PV', 'LL_CT', 'LL_KC', 'LL_KL'])],
            'parent_id' => ['nullable', 'integer'],
        ]);

        $level = strtoupper((string) $payload['level']);
        $parentId = isset($payload['parent_id']) ? (int) $payload['parent_id'] : null;

        $options = $this->guestBookingService->getLocationOptions($level, $parentId);

        return response()->json([
            'options' => $options,
        ]);
    }

    public function availability(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'date' => ['required', 'date'],
        ]);

        $availability = $this->guestBookingService->getDateAvailability((string) $payload['date']);

        return response()->json($availability);
    }

    public function estimate(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'package_id' => ['required', 'integer'],
            'location_province_id' => ['nullable', 'integer'],
            'location_city_id' => ['nullable', 'integer'],
            'location_district_id' => ['nullable', 'integer'],
            'location_village_id' => ['nullable', 'integer'],
            'event_date' => ['nullable', 'date'],
        ]);

        try {
            $estimate = $this->guestBookingService->getPriceEstimate($payload);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json($estimate);
    }
}
