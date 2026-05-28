<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\BookingBillingDetailStoreRequest;
use App\Http\Requests\Api\Admin\BookingCancelRequest;
use App\Http\Requests\Api\Admin\BookingForceMajeureRequest;
use App\Http\Requests\Api\Admin\BookingInstallmentStoreRequest;
use App\Http\Requests\Api\Admin\BookingApproveRequest;
use App\Http\Requests\Api\Admin\BookingRejectManualRequest;
use App\Http\Requests\Api\Admin\BookingRejectRequest;
use App\Http\Requests\Api\Admin\BookingRefundProofRequest;
use App\Http\Requests\Api\Admin\BookingUploadPaymentRequest;
use App\Services\Admin\BookingDetailService;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class BookingDetailController extends Controller
{
    public function __construct(
        private BookingDetailService $bookingDetailService
    ) {}

    public function approve(int $booking, BookingApproveRequest $request): JsonResponse
    {
        $operatorId = auth()->id();

        $booking = $this->bookingDetailService->approveBooking($booking, $operatorId);

        return response()->json([
            'message' => 'Booking berhasil di-approve. Billing DP telah dibuat.',
            'status_code' => $booking->status?->code,
        ]);
    }

    public function reject(int $booking, BookingRejectRequest $request): JsonResponse
    {
        $operatorId = auth()->id();

        $booking = $this->bookingDetailService->rejectBooking($booking, $operatorId);

        return response()->json([
            'message' => 'Booking berhasil ditolak.',
            'status_code' => $booking->status?->code,
        ]);
    }

    public function uploadPayment(int $booking, BookingUploadPaymentRequest $request): JsonResponse
    {
        $operatorId = auth()->id();

        try {
            $updatedBooking = $this->bookingDetailService->uploadManualPayment(
                $booking,
                (int) $operatorId,
                $request->payload()
            );
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Pembayaran berhasil dicatat.',
            'status_code' => $updatedBooking->status?->code,
        ]);
    }

    public function verifyDp(int $booking): JsonResponse
    {
        $operatorId = auth()->id();

        try {
            $updatedBooking = $this->bookingDetailService->verifyDp($booking, $operatorId);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'DP berhasil diverifikasi. Status booking diubah ke Waiting Final Payment.',
            'status_code' => $updatedBooking->status?->code,
        ]);
    }

    public function rejectManual(int $booking, BookingRejectManualRequest $request): JsonResponse
    {
        $operatorId = auth()->id();

        try {
            $updatedBooking = $this->bookingDetailService->rejectManual($booking, $operatorId, $request->reason());
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Booking berhasil ditolak.',
            'status_code' => $updatedBooking->status?->code,
        ]);
    }

    public function verifyFinalPayment(int $booking): JsonResponse
    {
        $operatorId = auth()->id();

        try {
            $updatedBooking = $this->bookingDetailService->verifyFinalPayment($booking, $operatorId);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Pelunasan terverifikasi. Booking confirmed.',
            'status_code' => $updatedBooking->status?->code,
        ]);
    }

    public function cancelBooking(int $booking, BookingCancelRequest $request): JsonResponse
    {
        $operatorId = auth()->id();

        try {
            $updatedBooking = $this->bookingDetailService->cancelBooking($booking, $operatorId, $request->reason());
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Booking berhasil dibatalkan.',
            'status_code' => $updatedBooking->status?->code,
        ]);
    }

    public function completeBooking(int $booking): JsonResponse
    {
        $operatorId = auth()->id();

        try {
            $updatedBooking = $this->bookingDetailService->completeBooking($booking, $operatorId);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Booking selesai.',
            'status_code' => $updatedBooking->status?->code,
        ]);
    }

    public function forceMajeure(int $booking, BookingForceMajeureRequest $request): JsonResponse
    {
        $operatorId = auth()->id();

        try {
            if ($request->isReschedule()) {
                $updatedBooking = $this->bookingDetailService->forceMajeureReschedule(
                    $booking,
                    $operatorId,
                    $request->reason(),
                    $request->newDate()
                );
            } else {
                $updatedBooking = $this->bookingDetailService->forceMajeureRefund(
                    $booking,
                    $operatorId,
                    $request->reason()
                );
            }
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        $message = $request->isReschedule()
            ? 'Force majeure - reschedule berhasil diproses.'
            : 'Force majeure - refund diproses.';

        return response()->json([
            'message' => $message,
            'status_code' => $updatedBooking->status?->code,
        ]);
    }

    public function uploadRefundProof(int $booking, BookingRefundProofRequest $request): JsonResponse
    {
        $operatorId = auth()->id();

        try {
            $updatedBooking = $this->bookingDetailService->uploadRefundProof(
                $booking,
                $operatorId,
                $request->payload()
            );
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Bukti refund berhasil dicatat.',
            'status_code' => $updatedBooking->status?->code,
        ]);
    }

    public function storeBillingDetail(int $booking, BookingBillingDetailStoreRequest $request): JsonResponse
    {
        $operatorId = auth()->id();

        try {
            $updatedBooking = $this->bookingDetailService->addBillingDetail(
                $booking,
                (int) $operatorId,
                $request->payload()
            );
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Komponen billing berhasil ditambahkan.',
            'status_code' => $updatedBooking->status?->code,
        ]);
    }

    public function storeInstallment(int $booking, BookingInstallmentStoreRequest $request): JsonResponse
    {
        $operatorId = auth()->id();

        try {
            $updatedBooking = $this->bookingDetailService->generateInstallment(
                $booking,
                (int) $operatorId,
                $request->payload()
            );
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Tagihan installment berhasil dibuat.',
            'status_code' => $updatedBooking->status?->code,
        ]);
    }
}
