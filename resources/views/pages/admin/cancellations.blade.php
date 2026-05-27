@extends('layouts.admin.admin')

@section('title', $title)

@section('content')
@php
    $actions = [
        ['label' => 'Customers', 'url' => panel_route('admin.customers'), 'class' => 'btn btn-outline-primary btn-sm'],
        ['label' => 'Bookings Active', 'url' => panel_route('admin.bookings.active'), 'class' => 'btn btn-primary btn-sm'],
    ];

    $alerts = [
        ['class' => 'alert-danger', 'text' => 'Sesuai kebijakan, DP bersifat non-refundable saat booking dibatalkan.'],
    ];

    $columns = ['Kode', 'Customer', 'Tanggal Acara', 'Nilai DP', 'Status', 'Catatan'];
    $rows = [
        ['ETH-2026-005', 'Nina Ayu', '2026-06-28', 'IDR 1.800.000', ['type' => 'badge', 'tone' => 'danger', 'label' => 'cancelled'], 'DP hangus sesuai policy cancellation'],
        ['ETH-2026-004', 'Ari Prakoso', '2026-06-26', 'IDR 600.000', ['type' => 'badge', 'tone' => 'danger', 'label' => 'cancelled'], 'Pembatalan dari customer karena perubahan acara'],
    ];

    $sideCards = [
        [
            'title' => 'Policy Cancellation',
            'bullets' => [
                'DP tidak dapat dikembalikan (non-refundable).',
                'Status booking diubah ke cancelled.',
                'Catatan pembatalan wajib terdokumentasi untuk audit.',
            ],
        ],
    ];
@endphp

@include('pages.admin.partials.page-header', [
    'heading' => 'Cancellations',
    'summary' => 'Pencatatan pembatalan booking dan dampaknya terhadap pembayaran DP.',
    'actions' => $actions,
])

@include('pages.admin.partials.alerts', ['alerts' => $alerts])

<div class="row g-3">
    <div class="col-12 col-xl-8">
        @include('pages.admin.partials.data-table', [
            'tableTitle' => 'Riwayat Cancellation',
            'tableBadge' => 'DP Non-Refundable',
            'columns' => $columns,
            'rows' => $rows,
        ])
    </div>
    <div class="col-12 col-xl-4">
        @include('pages.admin.partials.side-cards', ['cards' => $sideCards])
    </div>
</div>
@endsection

