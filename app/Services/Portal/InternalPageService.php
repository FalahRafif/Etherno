<?php

namespace App\Services\Portal;

use Illuminate\View\View;
use RuntimeException;

class InternalPageService
{
    /**
     * @var array<string, array{view:string,title:string}>
     */
    private array $pages = [
        'dashboard' => ['view' => 'pages.admin.dashboard', 'title' => 'Dashboard'],
        'bookings.list' => ['view' => 'pages.admin.bookings.list', 'title' => 'Daftar Booking'],
        'bookings.requests' => ['view' => 'pages.admin.bookings.requests', 'title' => 'Booking Requests'],
        'bookings.active' => ['view' => 'pages.admin.bookings.active', 'title' => 'Bookings Active'],
        'bookings.detail' => ['view' => 'pages.admin.bookings.detail', 'title' => 'Booking Detail'],
        'calendar' => ['view' => 'pages.admin.calendar', 'title' => 'Calendar & Slots'],
        'payments.dp' => ['view' => 'pages.admin.payments.dp', 'title' => 'DP Verification'],
        'payments.final' => ['view' => 'pages.admin.payments.final', 'title' => 'Final Payment'],
        'pricing.reviews' => ['view' => 'pages.admin.pricing.reviews', 'title' => 'Pricing Review'],
        'packages' => ['view' => 'pages.admin.master.packages', 'title' => 'Packages'],
        'reschedules' => ['view' => 'pages.admin.reschedules', 'title' => 'Reschedule Requests'],
        'cancellations' => ['view' => 'pages.admin.cancellations', 'title' => 'Cancellations'],
        'force.majeure' => ['view' => 'pages.admin.force-majeure', 'title' => 'Force Majeure'],
        'customers' => ['view' => 'pages.admin.customers', 'title' => 'Customers'],
        'settings' => ['view' => 'pages.admin.settings', 'title' => 'Settings'],
    ];

    public function render(string $panelPrefix, string $pageKey, array $payload = []): View
    {
        if (!isset($this->pages[$pageKey])) {
            throw new RuntimeException("Internal page config '{$pageKey}' not found.");
        }

        $page = $this->pages[$pageKey];
        $panelName = config("role_access.panel_title_by_prefix.{$panelPrefix}", ucfirst($panelPrefix));

        return view($page['view'], array_merge([
            'title' => "{$page['title']} - Etherno {$panelName}",
        ], $payload));
    }
}
