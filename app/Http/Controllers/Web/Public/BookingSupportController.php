<?php

namespace App\Http\Controllers\Web\Public;

use App\Http\Controllers\Controller;
use App\Services\Portal\GuestBookingService;
use App\Services\Portal\GuestPageService;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BookingSupportController extends Controller
{
    public function __construct(
        private GuestPageService $guestPageService,
        private GuestBookingService $guestBookingService
    )
    {
    }

    public function success(): View
    {
        return $this->guestPageService->render('booking.success');
    }

    public function status(): View
    {
        return $this->guestPageService->render('booking.status');
    }

    public function reschedule(): View
    {
        return $this->guestPageService->render('booking.reschedule');
    }

    public function cancellationPolicy(): View
    {
        return $this->guestPageService->render('booking.cancellation.policy');
    }

    public function downloadSubmissionProof(string $bookingUuid): BinaryFileResponse
    {
        try {
            $booking = $this->guestBookingService->getBookingForSubmissionProofByUuidOrFail($bookingUuid);
            $proofFile = $this->guestBookingService->ensureSubmissionProofDocument($booking);
        } catch (RuntimeException) {
            abort(404, 'Dokumen bukti pengajuan tidak ditemukan.');
        }

        $relativePath = (string) ($proofFile['path'] ?? '');
        $downloadName = (string) ($proofFile['filename'] ?? 'bukti-pengajuan-booking.pdf');
        if ($relativePath === '' || !Storage::disk('local')->exists($relativePath)) {
            abort(404, 'Dokumen bukti pengajuan tidak ditemukan.');
        }

        return response()->download(
            Storage::disk('local')->path($relativePath),
            $downloadName,
            ['Content-Type' => 'application/pdf']
        );
    }
}
