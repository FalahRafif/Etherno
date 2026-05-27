<?php

namespace App\Http\Controllers\Web\Public;

use App\Http\Controllers\Controller;
use App\Services\Portal\GuestPageService;
use Illuminate\View\View;

class BookingSupportController extends Controller
{
    public function __construct(private GuestPageService $guestPageService)
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

    public function dpPayment(): View
    {
        return $this->guestPageService->render('booking.payment.dp');
    }

    public function finalPayment(): View
    {
        return $this->guestPageService->render('booking.payment.final');
    }

    public function reschedule(): View
    {
        return $this->guestPageService->render('booking.reschedule');
    }

    public function cancellationPolicy(): View
    {
        return $this->guestPageService->render('booking.cancellation.policy');
    }
}
