@extends('layouts.admin.admin')

@section('title', $title)

@section('content')
@php
    $actions = [
        ['label' => 'DP Verification', 'url' => panel_route('admin.payments.dp'), 'class' => 'btn btn-outline-primary btn-sm'],
        ['label' => 'Bookings Active', 'url' => panel_route('admin.bookings.active'), 'class' => 'btn btn-primary btn-sm'],
    ];

    $alerts = [
        ['class' => 'alert-warning', 'text' => 'Pelunasan wajib selesai maksimal H-1 acara. Konfirmasi utama tetap melalui WhatsApp, upload bukti di sistem hanya opsional.'],
    ];

    $stats = [
        ['label' => 'Final Pending', 'value' => '5', 'hint' => 'Menunggu verifikasi admin', 'tone' => 'warning'],
        ['label' => 'Due H-1', 'value' => '3', 'hint' => 'Perlu follow-up prioritas', 'tone' => 'danger'],
        ['label' => 'Final Verified', 'value' => '12', 'hint' => 'Status booking paid', 'tone' => 'success'],
        ['label' => 'Risk Lewat Deadline', 'value' => '2', 'hint' => 'Potensi terlambat bayar', 'tone' => 'secondary'],
    ];

    $columns = ['Kode', 'Tanggal Acara', 'Sisa Tagihan', 'Konfirmasi', 'Status', 'Aksi'];
    $rows = [
        [
            'ETH-2026-018',
            '2026-06-19',
            'IDR 10.200.000',
            ['type' => 'stack', 'primary' => 'WhatsApp + Upload', 'secondary' => 'Masuk 17:20 WIB'],
            ['type' => 'badge', 'tone' => 'danger', 'label' => 'final_pending_verification'],
            ['type' => 'link', 'label' => 'Verifikasi', 'url' => panel_route('admin.bookings.detail', ['booking' => 'eth-2026-018'])],
        ],
        [
            'ETH-2026-024',
            '2026-07-01',
            'IDR 0',
            ['type' => 'stack', 'primary' => 'WhatsApp', 'secondary' => 'Terverifikasi manual'],
            ['type' => 'badge', 'tone' => 'primary', 'label' => 'final_verified'],
            ['type' => 'link', 'label' => 'Detail', 'url' => panel_route('admin.bookings.detail', ['booking' => 'eth-2026-024'])],
        ],
        [
            'ETH-2026-026',
            '2026-06-22',
            'IDR 5.400.000',
            ['type' => 'stack', 'primary' => 'Upload Bukti', 'secondary' => 'Menunggu konfirmasi WA'],
            ['type' => 'badge', 'tone' => 'warning', 'label' => 'final_pending_verification'],
            ['type' => 'link', 'label' => 'Review', 'url' => panel_route('admin.bookings.detail', ['booking' => 'eth-2026-026'])],
        ],
    ];

    $sideCards = [
        [
            'title' => 'Kebijakan Pelunasan',
            'bullets' => [
                'Jatuh tempo pelunasan maksimal H-1 acara.',
                'Verifikasi tetap manual oleh admin.',
                'Status paid hanya untuk pembayaran final yang valid.',
                'Keterlambatan harus dicatat sebagai risiko operasional.',
            ],
        ],
        [
            'title' => 'Aksi Cepat',
            'actions' => [
                ['label' => 'Buka Booking Aktif', 'url' => panel_route('admin.bookings.active'), 'class' => 'btn btn-outline-primary btn-sm'],
                ['label' => 'Buka Pricing Review', 'url' => panel_route('admin.pricing.reviews'), 'class' => 'btn btn-outline-primary btn-sm'],
            ],
        ],
    ];
@endphp

@include('pages.admin.partials.page-header', [
    'heading' => 'Final Payment',
    'summary' => 'Verifikasi pelunasan akhir sebelum acara berlangsung untuk menjaga kepatuhan deadline H-1.',
    'actions' => $actions,
])

@include('pages.admin.partials.alerts', ['alerts' => $alerts])
@include('pages.admin.partials.stats-grid', ['stats' => $stats])

<div class="row g-3">
    <div class="col-12 col-xl-8">
        @include('pages.admin.partials.data-table', [
            'tableTitle' => 'Queue Verifikasi Pelunasan',
            'tableBadge' => 'H-1 Priority',
            'columns' => $columns,
            'rows' => $rows,
        ])
    </div>
    <div class="col-12 col-xl-4">
        @include('pages.admin.partials.side-cards', ['cards' => $sideCards])
    </div>
</div>
@endsection

