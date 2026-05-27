@extends('layouts.admin.admin')

@section('title', $title)

@section('content')
@php
    $actions = [
        ['label' => 'Final Payment Queue', 'url' => panel_route('admin.payments.final'), 'class' => 'btn btn-outline-primary btn-sm'],
        ['label' => 'Booking Requests', 'url' => panel_route('admin.bookings.requests'), 'class' => 'btn btn-primary btn-sm'],
    ];

    $alerts = [
        ['class' => 'alert-info', 'text' => 'Verifikasi DP tetap manual oleh admin. Upload bukti di sistem hanya support flow dan tidak otomatis mengubah status booking menjadi aktif.'],
    ];

    $stats = [
        ['label' => 'DP Pending', 'value' => '7', 'hint' => 'Perlu verifikasi hari ini', 'tone' => 'warning'],
        ['label' => 'DP Verified Hari Ini', 'value' => '3', 'hint' => 'Booking aktif otomatis', 'tone' => 'success'],
        ['label' => 'Need Recheck', 'value' => '1', 'hint' => 'Data transfer tidak sinkron', 'tone' => 'danger'],
        ['label' => 'DP Expired', 'value' => '2', 'hint' => 'Lewat 3 hari dari approval', 'tone' => 'secondary'],
    ];

    $columns = ['Kode', 'Customer', 'Jenis', 'DP', 'Konfirmasi', 'Status', 'SLA', 'Aksi'];
    $rows = [
        [
            'ETH-2026-022',
            'Raka & Mila',
            'Wedding',
            'IDR 1.800.000 (15%)',
            ['type' => 'stack', 'primary' => 'WhatsApp + Upload Bukti', 'secondary' => 'Transfer BCA 10:23 WIB'],
            ['type' => 'badge', 'tone' => 'warning', 'label' => 'dp_pending_verification'],
            'Hari ke-1',
            ['type' => 'link', 'label' => 'Verifikasi', 'url' => panel_route('admin.bookings.detail', ['booking' => 'eth-2026-022'])],
        ],
        [
            'ETH-2026-023',
            'Ari Susanto',
            'Non-wedding',
            'IDR 600.000 (10%)',
            ['type' => 'stack', 'primary' => 'WhatsApp', 'secondary' => 'Belum upload bukti di sistem'],
            ['type' => 'badge', 'tone' => 'warning', 'label' => 'dp_pending_verification'],
            'Hari ke-2',
            ['type' => 'link', 'label' => 'Verifikasi', 'url' => panel_route('admin.bookings.detail', ['booking' => 'eth-2026-023'])],
        ],
        [
            'ETH-2026-025',
            'Salsa Dewi',
            'Wedding',
            'IDR 1.800.000 (15%)',
            ['type' => 'stack', 'primary' => 'Upload Bukti', 'secondary' => 'Tidak ada konfirmasi WA'],
            ['type' => 'badge', 'tone' => 'danger', 'label' => 'need_recheck'],
            'Hari ke-3',
            ['type' => 'link', 'label' => 'Review', 'url' => panel_route('admin.bookings.detail', ['booking' => 'eth-2026-025'])],
        ],
    ];

    $sideCards = [
        [
            'title' => 'Checklist Verifikasi DP',
            'bullets' => [
                'Cocokkan nominal transfer terhadap skema DP (15% wedding, 10% non-wedding).',
                'Pastikan ada konfirmasi melalui WhatsApp.',
                'Set status booking ke active hanya setelah validasi selesai.',
                'Jika lewat 3 hari dari approval dan belum valid, set expired.',
            ],
        ],
        [
            'title' => 'Status Payment',
            'items' => [
                ['label' => 'Sebelum transfer', 'value' => 'dp_waiting_payment'],
                ['label' => 'Sudah konfirmasi', 'value' => 'dp_pending_verification'],
                ['label' => 'Sudah valid', 'value' => 'dp_verified'],
                ['label' => 'Lewat batas', 'value' => 'expired'],
            ],
        ],
    ];
@endphp

@include('pages.admin.partials.page-header', [
    'heading' => 'DP Verification',
    'summary' => 'Antrian verifikasi pembayaran DP untuk mengubah booking menjadi aktif dan mengunci slot.',
    'actions' => $actions,
])

@include('pages.admin.partials.alerts', ['alerts' => $alerts])
@include('pages.admin.partials.stats-grid', ['stats' => $stats])

<div class="row g-3">
    <div class="col-12 col-xl-8">
        @include('pages.admin.partials.data-table', [
            'tableTitle' => 'Queue Verifikasi DP',
            'tableBadge' => 'Manual Check',
            'columns' => $columns,
            'rows' => $rows,
        ])
    </div>
    <div class="col-12 col-xl-4">
        @include('pages.admin.partials.side-cards', ['cards' => $sideCards])
    </div>
</div>
@endsection

