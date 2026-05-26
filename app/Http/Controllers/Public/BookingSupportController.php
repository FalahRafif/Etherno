<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;

class BookingSupportController extends Controller
{
    public function success()
    {
        return view('pages.public.booking-page.support.success', [
            'title' => 'Request Booking Terkirim - Etherno',
        ]);
    }

    public function status()
    {
        return view('pages.public.booking-page.support.status', [
            'title' => 'Cek Status Booking - Etherno',
        ]);
    }

    public function dpPayment()
    {
        return view('pages.public.booking-page.support.payment-dp', [
            'title' => 'Konfirmasi DP - Etherno',
        ]);
    }

    public function finalPayment()
    {
        return view('pages.public.booking-page.support.payment-final', [
            'title' => 'Konfirmasi Pelunasan - Etherno',
        ]);
    }

    public function reschedule()
    {
        return view('pages.public.booking-page.support.reschedule', [
            'title' => 'Request Reschedule - Etherno',
        ]);
    }

    public function cancellationPolicy()
    {
        return view('pages.public.booking-page.support.cancellation-policy', [
            'title' => 'Kebijakan Pembatalan - Etherno',
        ]);
    }
}
