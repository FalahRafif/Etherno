<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminPreviewController extends Controller
{
    public function dashboard()
    {
        return $this->page('pages.admin.dashboard', 'Dashboard');
    }

    public function bookingRequests()
    {
        return $this->page('pages.admin.bookings.requests', 'Booking Requests');
    }

    public function bookingsActive()
    {
        return $this->page('pages.admin.bookings.active', 'Bookings Active');
    }

    public function bookingDetail(string $booking)
    {
        return $this->page('pages.admin.bookings.detail', 'Booking Detail', [
            'bookingCode' => strtoupper($booking),
        ]);
    }

    public function calendar()
    {
        return $this->page('pages.admin.calendar', 'Calendar & Slots');
    }

    public function dpVerification()
    {
        return $this->page('pages.admin.payments.dp', 'DP Verification');
    }

    public function finalPayment()
    {
        return $this->page('pages.admin.payments.final', 'Final Payment');
    }

    public function pricingReviews()
    {
        return $this->page('pages.admin.pricing.reviews', 'Pricing Review');
    }

    public function packages()
    {
        return $this->page('pages.admin.master.packages', 'Packages');
    }

    public function locationRules()
    {
        return $this->page('pages.admin.master.location-rules', 'Location Rules');
    }

    public function reschedules()
    {
        return $this->page('pages.admin.reschedules', 'Reschedule Requests');
    }

    public function cancellations()
    {
        return $this->page('pages.admin.cancellations', 'Cancellations');
    }

    public function forceMajeure()
    {
        return $this->page('pages.admin.force-majeure', 'Force Majeure');
    }

    public function customers()
    {
        return $this->page('pages.admin.customers', 'Customers');
    }

    public function settings()
    {
        return $this->page('pages.admin.settings', 'Settings');
    }

    protected function page(string $view, string $title, array $payload = [])
    {
        return view($view, array_merge([
            'title' => $title . ' - Etherno Admin',
        ], $payload));
    }
}
