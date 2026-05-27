@extends('layouts.admin.admin')

@section('title', $title)

@section('content')
@php
    $actions = [
        ['label' => 'Booking Requests', 'url' => panel_route('admin.bookings.requests'), 'class' => 'btn btn-outline-primary btn-sm'],
        ['label' => 'Verifikasi DP', 'url' => panel_route('admin.payments.dp'), 'class' => 'btn btn-outline-primary btn-sm'],
        ['label' => 'Calendar & Slots', 'url' => panel_route('admin.calendar'), 'class' => 'btn btn-primary btn-sm'],
    ];

    $alerts = [
        [
            'class' => 'alert-warning',
            'text' => 'Reminder: slot hanya dianggap terblokir jika status booking sudah active/paid setelah DP terverifikasi.',
        ],
    ];

    $stats = [
        ['label' => 'Request Baru', 'value' => '14', 'hint' => 'Status submitted / under_review', 'tone' => 'warning'],
        ['label' => 'Approved Menunggu DP', 'value' => '9', 'hint' => 'Belum mengunci slot', 'tone' => 'info'],
        ['label' => 'Booking Aktif', 'value' => '22', 'hint' => 'Slot terkunci (DP verified)', 'tone' => 'success'],
        ['label' => 'Pelunasan Jatuh Tempo H-1', 'value' => '5', 'hint' => 'Perlu follow-up WhatsApp', 'tone' => 'danger'],
    ];

    $columns = ['Kode', 'Customer', 'Tanggal', 'Status Booking', 'Status Payment', 'Tindak Lanjut'];
    $rows = [
        [
            'ETH-2026-021',
            'Nadia Putri',
            '2026-07-12',
            ['type' => 'badge', 'tone' => 'warning', 'label' => 'under_review'],
            ['type' => 'badge', 'tone' => 'light', 'label' => 'dp_waiting_payment'],
            ['type' => 'link', 'label' => 'Review', 'url' => panel_route('admin.bookings.detail', ['booking' => 'eth-2026-021'])],
        ],
        [
            'ETH-2026-014',
            'Rani Putri',
            '2026-06-25',
            ['type' => 'badge', 'tone' => 'success', 'label' => 'active'],
            ['type' => 'badge', 'tone' => 'info', 'label' => 'dp_verified'],
            ['type' => 'link', 'label' => 'Set Final Price', 'url' => panel_route('admin.pricing.reviews')],
        ],
        [
            'ETH-2026-018',
            'Dika & Lala',
            '2026-06-19',
            ['type' => 'badge', 'tone' => 'secondary', 'label' => 'final_payment_pending'],
            ['type' => 'badge', 'tone' => 'danger', 'label' => 'final_pending_verification'],
            ['type' => 'link', 'label' => 'Verifikasi Pelunasan', 'url' => panel_route('admin.payments.final')],
        ],
    ];

    $sideCards = [
        [
            'title' => 'Model Status Utama',
            'items' => [
                ['label' => 'Booking', 'value' => 'submitted, under_review, approved, active, paid'],
                ['label' => 'Payment', 'value' => 'dp_pending_verification, dp_verified, final_verified'],
                ['label' => 'Exception', 'value' => 'expired, cancelled, force_majeure'],
            ],
        ],
        [
            'title' => 'Prioritas Operasional',
            'bullets' => [
                'Verifikasi DP maksimal di hari yang sama untuk mempercepat locking slot.',
                'Final payment harus selesai maksimal H-1 acara.',
                'Semua koordinasi lanjutan tetap dipusatkan melalui WhatsApp.',
            ],
            'actions' => [
                ['label' => 'Buka DP Queue', 'url' => panel_route('admin.payments.dp'), 'class' => 'btn btn-primary btn-sm'],
            ],
        ],
    ];
@endphp

@include('pages.admin.partials.page-header', [
    'heading' => 'Dashboard',
    'summary' => 'Ringkasan operasional harian untuk booking flow, verifikasi pembayaran, dan kapasitas slot.',
    'actions' => $actions,
])

@include('pages.admin.partials.alerts', ['alerts' => $alerts])
@include('pages.admin.partials.stats-grid', ['stats' => $stats])

<div class="row g-3">
    <div class="col-12 col-xl-8">
        @include('pages.admin.partials.data-table', [
            'tableTitle' => 'Antrian Operasional',
            'tableBadge' => 'Today',
            'columns' => $columns,
            'rows' => $rows,
        ])
    </div>
    <div class="col-12 col-xl-4">
        @include('pages.admin.partials.side-cards', ['cards' => $sideCards])
    </div>
</div>
@endsection

