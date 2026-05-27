@extends('layouts.admin.admin')

@section('title', $title)

@section('content')
@php
    $actions = [
        ['label' => 'Bookings Active', 'url' => panel_route('admin.bookings.active'), 'class' => 'btn btn-outline-primary btn-sm'],
        ['label' => 'Customers', 'url' => panel_route('admin.customers'), 'class' => 'btn btn-primary btn-sm'],
    ];

    $alerts = [
        ['class' => 'alert-warning', 'text' => 'Force majeure ditangani manual. Refund (jika ada) tetap dikurangi biaya operasional sesuai kebijakan.'],
    ];

    $columns = ['Kode', 'Jenis Kondisi', 'Tindakan', 'Biaya', 'Status'];
    $rows = [
        ['ETH-2026-007', 'Fotografer berhalangan', 'Diganti fotografer cadangan', 'Tanpa biaya tambahan', ['type' => 'badge', 'tone' => 'success', 'label' => 'resolved']],
        ['ETH-2026-012', 'Cuaca ekstrem', 'Sesi dihentikan, dijadwalkan ulang', 'Operasional dihitung ulang', ['type' => 'badge', 'tone' => 'warning', 'label' => 'in_review']],
        ['ETH-2026-003', 'Bencana lokal', 'Refund parsial', 'Dikurangi biaya operasional', ['type' => 'badge', 'tone' => 'info', 'label' => 'closed']],
    ];

    $sideCards = [
        [
            'title' => 'Panduan Penanganan',
            'items' => [
                ['label' => 'Fotografer berhalangan', 'value' => 'Sediakan pengganti'],
                ['label' => 'Cuaca buruk', 'value' => 'Sesi bisa dihentikan/dipindah'],
                ['label' => 'Kondisi ekstrem', 'value' => 'Refund parsial (manual)'],
                ['label' => 'Catatan', 'value' => 'Semua komunikasi via WhatsApp'],
            ],
        ],
    ];
@endphp

@include('pages.admin.partials.page-header', [
    'heading' => 'Force Majeure',
    'summary' => 'Monitoring kasus pengecualian operasional: pengganti fotografer, cuaca buruk, hingga refund parsial.',
    'actions' => $actions,
])

@include('pages.admin.partials.alerts', ['alerts' => $alerts])

<div class="row g-3">
    <div class="col-12 col-xl-8">
        @include('pages.admin.partials.data-table', [
            'tableTitle' => 'Catatan Force Majeure',
            'tableBadge' => 'Exception Handling',
            'columns' => $columns,
            'rows' => $rows,
        ])
    </div>
    <div class="col-12 col-xl-4">
        @include('pages.admin.partials.side-cards', ['cards' => $sideCards])
    </div>
</div>
@endsection

