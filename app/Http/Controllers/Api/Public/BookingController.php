<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\Booking\StoreBookingRequest;
use App\Services\Portal\GuestBookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
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

        $proofDownloadUrl = null;
        try {
            $this->guestBookingService->ensureSubmissionProofDocument($booking);
            $proofDownloadUrl = URL::temporarySignedRoute('booking.proof.download', now()->addDays(7), [
                'bookingUuid' => $booking->uuid,
            ]);
        } catch (\Throwable) {
            $proofDownloadUrl = null;
        }

        $customerName = trim(implode(' ', array_filter([
            $booking->customer?->first_name,
            $booking->customer?->last_name,
        ])));

        $caseId = $this->guestBookingService->buildBookingCaseId($booking);
        $adminWhatsapp = $this->guestBookingService->getAdminWhatsApp();
        $whatsappTemplate = 'Halo tim Etherno, saya ' . ($customerName !== '' ? $customerName : 'Customer') . '. Saya baru saja mengajukan booking dengan Case ID ' . $caseId . '. Mohon informasi lebih lanjut. Terima kasih.';

        return redirect()
            ->route('booking.success')
            ->with([
                'booking_request_code' => $this->guestBookingService->buildRequestCode($booking),
                'booking_case_id' => $caseId,
                'booking_uuid' => $booking->uuid,
                'booking_customer_name' => $customerName !== '' ? $customerName : 'Customer',
                'booking_proof_download_url' => $proofDownloadUrl,
                'admin_whatsapp' => $adminWhatsapp,
                'whatsapp_template' => $whatsappTemplate,
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

    public function statusLookup(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'booking_code' => ['required', 'string', 'max:120'],
            'phone_last4' => ['required', 'digits:4'],
        ]);

        try {
            $statusPayload = $this->guestBookingService->getBookingStatusPayload(
                (string) $payload['booking_code'],
                (string) $payload['phone_last4']
            );
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        $bookingUuid = trim((string) ($statusPayload['booking_uuid'] ?? ''));
        $proofDownloadUrl = null;
        if ($bookingUuid !== '') {
            $proofDownloadUrl = URL::temporarySignedRoute('booking.proof.download', now()->addDays(7), [
                'bookingUuid' => $bookingUuid,
            ]);
        }

        return response()->json(array_merge($statusPayload, [
            'proof_download_url' => $proofDownloadUrl,
        ]));
    }

    public function uploadPaymentProof(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'booking_code' => ['required', 'string', 'max:120'],
            'phone_last4' => ['required', 'digits:4'],
            'billing_installment_id' => ['required', 'integer'],
            'transfer_receipt' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:10240'],
        ]);

        try {
            $result = $this->guestBookingService->uploadPaymentProof(
                (string) $payload['booking_code'],
                (string) $payload['phone_last4'],
                (int) $payload['billing_installment_id'],
                $payload['transfer_receipt'] ?? null
            );

            return response()->json([
                'message' => 'Bukti pembayaran berhasil dikirim. Tim kami akan memverifikasi pembayaran Anda.',
                'payment' => $result,
            ]);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }
    }
}
