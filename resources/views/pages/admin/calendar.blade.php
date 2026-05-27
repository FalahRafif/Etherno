@extends('layouts.admin.admin')

@section('title', $title)

@section('content')
@php
    $actions = [
        ['label' => 'Bookings Active', 'url' => panel_route('admin.bookings.active'), 'class' => 'btn btn-outline-primary btn-sm'],
        ['label' => 'Booking Requests', 'url' => panel_route('admin.bookings.requests'), 'class' => 'btn btn-primary btn-sm'],
    ];

    $alerts = [
        ['class' => 'alert-warning', 'text' => 'Kapasitas per hari maksimal 2 booking. Queue booking request tidak mengunci slot sampai DP diverifikasi.'],
    ];

    $stats = [
        ['label' => 'Hari Full Slot', 'value' => '4', 'hint' => '2 booking aktif dalam satu hari', 'tone' => 'danger'],
        ['label' => 'Slot Tersedia Minggu Ini', 'value' => '9', 'hint' => 'Masih bisa menerima booking', 'tone' => 'success'],
        ['label' => 'Request Menunggu Approval', 'value' => '11', 'hint' => 'Belum mengunci slot', 'tone' => 'warning'],
        ['label' => 'Aktif Minggu Ini', 'value' => '7', 'hint' => 'Sudah DP verified', 'tone' => 'primary'],
    ];

    $columns = ['Tanggal', 'Sesi Pagi-Siang', 'Sesi Sore-Malam', 'Aktif', 'Status Hari', 'Aksi'];
    $rows = [
        [
            '2026-06-20',
            ['type' => 'badge', 'tone' => 'warning', 'label' => 'Tersisa 1 slot'],
            ['type' => 'badge', 'tone' => 'success', 'label' => 'Tersedia'],
            '1 / 2',
            ['type' => 'badge', 'tone' => 'info', 'label' => 'Partially Filled'],
            ['type' => 'link', 'label' => 'Lihat Request', 'url' => panel_route('admin.bookings.requests')],
        ],
        [
            '2026-06-21',
            ['type' => 'badge', 'tone' => 'danger', 'label' => 'Penuh'],
            ['type' => 'badge', 'tone' => 'danger', 'label' => 'Penuh'],
            '2 / 2',
            ['type' => 'badge', 'tone' => 'danger', 'label' => 'Full Capacity'],
            ['type' => 'link', 'label' => 'Buka Active', 'url' => panel_route('admin.bookings.active')],
        ],
        [
            '2026-06-22',
            ['type' => 'badge', 'tone' => 'success', 'label' => 'Tersedia'],
            ['type' => 'badge', 'tone' => 'success', 'label' => 'Tersedia'],
            '0 / 2',
            ['type' => 'badge', 'tone' => 'light', 'label' => 'Open'],
            ['type' => 'link', 'label' => 'Review Request', 'url' => panel_route('admin.bookings.requests')],
        ],
    ];

    $sideCards = [
        [
            'title' => 'Rules Capacity',
            'bullets' => [
                'First Come First Serve berdasarkan DP verified.',
                'Status approved tanpa DP belum memblokir slot.',
                'Expired otomatis membuka slot kembali.',
                'Reschedule tetap harus cek ketersediaan ulang.',
            ],
        ],
        [
            'title' => 'Sumber Data Slot',
            'items' => [
                ['label' => 'Memblokir slot', 'value' => 'active, paid'],
                ['label' => 'Tidak memblokir', 'value' => 'submitted, approved, expired'],
                ['label' => 'Batas sesi', 'value' => 'Pagi-Siang / Sore-Malam'],
            ],
        ],
    ];
@endphp

@include('pages.admin.partials.page-header', [
    'heading' => 'Calendar & Slots',
    'summary' => 'Monitoring kapasitas harian untuk memastikan aturan slot maksimal dua booking per hari tetap terjaga.',
    'actions' => $actions,
])

@include('pages.admin.partials.alerts', ['alerts' => $alerts])
@include('pages.admin.partials.stats-grid', ['stats' => $stats])

<div class="row g-3">
    <div class="col-12 col-xl-8">
        @include('pages.admin.partials.data-table', [
            'tableTitle' => 'Ringkasan Slot Harian',
            'tableBadge' => 'Capacity 2/Day',
            'columns' => $columns,
            'rows' => $rows,
        ])
    </div>
    <div class="col-12 col-xl-4">
        @include('pages.admin.partials.side-cards', ['cards' => $sideCards])
    </div>
</div>
@endsection

