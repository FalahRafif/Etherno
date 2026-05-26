@extends('layouts.admin.admin')

@section('title', $title)

@section('content')
@php
    $actions = [
        ['label' => 'Bookings Active', 'url' => route('admin.bookings.active'), 'class' => 'btn btn-outline-primary btn-sm'],
        ['label' => 'Calendar & Slots', 'url' => route('admin.calendar'), 'class' => 'btn btn-outline-primary btn-sm'],
        ['label' => 'DP Verification', 'url' => route('admin.payments.dp'), 'class' => 'btn btn-primary btn-sm'],
    ];

    $alerts = [
        [
            'class' => 'alert-info',
            'text' => 'Flow wajib: review data booking dulu, kemudian approval, baru customer diarahkan melakukan pembayaran DP.',
        ],
    ];

    $stats = [
        ['label' => 'Submitted Hari Ini', 'value' => '6', 'hint' => 'Masuk dari form booking publik', 'tone' => 'info'],
        ['label' => 'Under Review', 'value' => '14', 'hint' => 'Menunggu keputusan admin', 'tone' => 'warning'],
        ['label' => 'Approved Menunggu DP', 'value' => '9', 'hint' => 'Belum memblokir slot', 'tone' => 'primary'],
        ['label' => 'Expired (3 Hari)', 'value' => '3', 'hint' => 'Perlu booking ulang jika lanjut', 'tone' => 'danger'],
    ];

    $columns = ['Kode', 'Nama', 'Jenis', 'Tanggal', 'Sesi', 'Paket', 'Lokasi', 'Maps Pin', 'Status', 'Aksi'];
    $rows = [
        [
            'ETH-2026-021',
            'Nadia Putri',
            'Wedding',
            '2026-07-12',
            'Pagi-Siang',
            'Andalan',
            'Bandung',
            ['type' => 'badge', 'tone' => 'success', 'label' => 'Valid'],
            ['type' => 'badge', 'tone' => 'warning', 'label' => 'under_review'],
            ['type' => 'link', 'label' => 'Review', 'url' => route('admin.bookings.detail', ['booking' => 'eth-2026-021'])],
        ],
        [
            'ETH-2026-022',
            'Raka & Mila',
            'Wedding',
            '2026-07-13',
            'Sore-Malam',
            'Mewah',
            'Jakarta',
            ['type' => 'badge', 'tone' => 'success', 'label' => 'Valid'],
            ['type' => 'badge', 'tone' => 'info', 'label' => 'approved'],
            ['type' => 'link', 'label' => 'Detail', 'url' => route('admin.bookings.detail', ['booking' => 'eth-2026-022'])],
        ],
        [
            'ETH-2026-023',
            'Ari Susanto',
            'Non-wedding',
            '2026-07-16',
            'Pagi-Siang',
            'Intim',
            'Yogyakarta',
            ['type' => 'badge', 'tone' => 'danger', 'label' => 'Missing'],
            ['type' => 'badge', 'tone' => 'light', 'label' => 'submitted'],
            ['type' => 'link', 'label' => 'Follow Up', 'url' => route('admin.bookings.detail', ['booking' => 'eth-2026-023'])],
        ],
    ];

    $sideCards = [
        [
            'title' => 'Checklist Review Wajib',
            'bullets' => [
                'Validasi tanggal, sesi, dan kapasitas harian (maksimal 2 booking/hari).',
                'Pastikan pin Google Maps terisi dengan benar.',
                'Pastikan jenis acara untuk skema DP (Wedding 15%, Non-wedding 10%).',
                'Set status approved hanya jika data siap masuk proses DP.',
            ],
        ],
        [
            'title' => 'Field Form Booking',
            'items' => [
                ['label' => 'Nama Customer', 'value' => 'Wajib'],
                ['label' => 'Nomor WhatsApp', 'value' => 'Wajib'],
                ['label' => 'Tanggal + Sesi', 'value' => 'Wajib'],
                ['label' => 'Lokasi + Pin Maps', 'value' => 'Wajib'],
                ['label' => 'Paket + Detail Acara', 'value' => 'Wajib'],
            ],
        ],
    ];
@endphp

@include('pages.admin.partials.page-header', [
    'heading' => 'Booking Requests',
    'summary' => 'Antrian request booking sebelum pembayaran DP. Request yang belum approved tidak boleh diarahkan untuk locking slot.',
    'actions' => $actions,
])

@include('pages.admin.partials.alerts', ['alerts' => $alerts])
@include('pages.admin.partials.stats-grid', ['stats' => $stats])

<div class="row g-3">
    <div class="col-12 col-xl-8">
        @include('pages.admin.partials.data-table', [
            'tableTitle' => 'Approval Queue',
            'tableBadge' => 'Pre-DP',
            'columns' => $columns,
            'rows' => $rows,
        ])
    </div>
    <div class="col-12 col-xl-4">
        @include('pages.admin.partials.side-cards', ['cards' => $sideCards])
    </div>
</div>
@endsection
