@extends('layouts.admin.admin')

@section('title', $title)

@section('content')
@php
    $actions = [
        ['label' => 'Calendar & Slots', 'url' => panel_route('admin.calendar'), 'class' => 'btn btn-primary btn-sm'],
        ['label' => 'Final Payment Queue', 'url' => panel_route('admin.payments.final'), 'class' => 'btn btn-outline-primary btn-sm'],
    ];

    $alerts = [
        [
            'class' => 'alert-success',
            'text' => 'Daftar ini hanya menampilkan booking dengan DP verified atau pelunasan verified agar logika slot tetap konsisten.',
        ],
    ];

    $stats = [
        ['label' => 'Total Aktif Bulan Ini', 'value' => '22', 'hint' => 'Status active / paid', 'tone' => 'success'],
        ['label' => 'Slot Pagi Terisi', 'value' => '14', 'hint' => 'Booking DP verified', 'tone' => 'primary'],
        ['label' => 'Slot Sore Terisi', 'value' => '12', 'hint' => 'Booking DP verified', 'tone' => 'primary'],
        ['label' => 'Hari Full Capacity', 'value' => '4', 'hint' => '2 booking terisi', 'tone' => 'danger'],
    ];

    $columns = ['Kode', 'Customer', 'Tanggal', 'Sesi', 'Lokasi', 'Status Booking', 'Status Payment', 'Aksi'];
    $rows = [
        [
            'ETH-2026-014',
            'Rani Putri',
            '2026-06-25',
            'Pagi-Siang',
            'Bandung',
            ['type' => 'badge', 'tone' => 'success', 'label' => 'active'],
            ['type' => 'badge', 'tone' => 'info', 'label' => 'dp_verified'],
            ['type' => 'link', 'label' => 'Detail', 'url' => panel_route('admin.bookings.detail', ['booking' => 'eth-2026-014'])],
        ],
        [
            'ETH-2026-018',
            'Dika & Lala',
            '2026-06-19',
            'Sore-Malam',
            'Jakarta',
            ['type' => 'badge', 'tone' => 'success', 'label' => 'active'],
            ['type' => 'badge', 'tone' => 'danger', 'label' => 'final_pending_verification'],
            ['type' => 'link', 'label' => 'Verifikasi Final', 'url' => panel_route('admin.payments.final')],
        ],
        [
            'ETH-2026-024',
            'Dewi Rahma',
            '2026-07-01',
            'Pagi-Siang',
            'Surabaya',
            ['type' => 'badge', 'tone' => 'primary', 'label' => 'paid'],
            ['type' => 'badge', 'tone' => 'primary', 'label' => 'final_verified'],
            ['type' => 'link', 'label' => 'Detail', 'url' => panel_route('admin.bookings.detail', ['booking' => 'eth-2026-024'])],
        ],
    ];

    $sideCards = [
        [
            'title' => 'Aturan Slot',
            'bullets' => [
                'Maksimal 2 booking aktif per hari.',
                'Sesi dibagi Pagi-Siang dan Sore-Malam.',
                'First Come First Serve berdasarkan DP verified.',
                'Approved tapi belum DP tidak boleh memblokir slot.',
            ],
        ],
        [
            'title' => 'Koordinasi Lanjutan',
            'items' => [
                ['label' => 'Kanal utama', 'value' => 'WhatsApp'],
                ['label' => 'Status sistem', 'value' => 'Ringkasan tracking'],
                ['label' => 'Arah komunikasi', 'value' => 'Manual dan terdokumentasi'],
            ],
        ],
    ];
@endphp

@include('pages.admin.partials.page-header', [
    'heading' => 'Bookings Active',
    'summary' => 'Booking yang sudah sah mengunci slot karena DP/pelunasan telah diverifikasi manual.',
    'actions' => $actions,
])

@include('pages.admin.partials.alerts', ['alerts' => $alerts])
@include('pages.admin.partials.stats-grid', ['stats' => $stats])

<div class="row g-3">
    <div class="col-12 col-xl-8">
        @include('pages.admin.partials.data-table', [
            'tableTitle' => 'Slot Locked Bookings',
            'tableBadge' => 'Active Only',
            'columns' => $columns,
            'rows' => $rows,
        ])
    </div>
    <div class="col-12 col-xl-4">
        @include('pages.admin.partials.side-cards', ['cards' => $sideCards])
    </div>
</div>
@endsection

