@extends('layouts.admin.admin')

@section('title', $title)

@section('content')
@php
    $actions = [
        ['label' => 'Booking Requests', 'url' => route('admin.bookings.requests'), 'class' => 'btn btn-outline-primary btn-sm'],
        ['label' => 'Cancellations', 'url' => route('admin.cancellations'), 'class' => 'btn btn-primary btn-sm'],
    ];

    $columns = ['Nama', 'WhatsApp', 'Total Booking', 'Booking Terakhir', 'Status Terakhir', 'Aksi'];
    $rows = [
        ['Rani Putri', '08xxxxxxxx11', '2', '2026-06-25', ['type' => 'badge', 'tone' => 'success', 'label' => 'active'], ['type' => 'link', 'label' => 'Lihat Booking', 'url' => route('admin.bookings.detail', ['booking' => 'eth-2026-014'])]],
        ['Ari Susanto', '08xxxxxxxx22', '1', '2026-07-16', ['type' => 'badge', 'tone' => 'warning', 'label' => 'under_review'], ['type' => 'link', 'label' => 'Lihat Booking', 'url' => route('admin.bookings.detail', ['booking' => 'eth-2026-023'])]],
        ['Nina Ayu', '08xxxxxxxx33', '1', '2026-06-28', ['type' => 'badge', 'tone' => 'danger', 'label' => 'cancelled'], ['type' => 'link', 'label' => 'Riwayat', 'url' => route('admin.cancellations')]],
    ];

    $sideCards = [
        [
            'title' => 'Customer Tracking',
            'bullets' => [
                'Identitas utama customer menggunakan nama dan nomor WhatsApp.',
                'Riwayat booking dipakai untuk follow-up layanan berikutnya.',
                'Status terakhir membantu admin menentukan prioritas komunikasi.',
            ],
        ],
    ];
@endphp

@include('pages.admin.partials.page-header', [
    'heading' => 'Customers',
    'summary' => 'Rekap customer dan histori booking sebagai dasar koordinasi dan layanan lanjutan.',
    'actions' => $actions,
])

<div class="row g-3">
    <div class="col-12 col-xl-8">
        @include('pages.admin.partials.data-table', [
            'tableTitle' => 'Daftar Customer',
            'tableBadge' => 'Relationship',
            'columns' => $columns,
            'rows' => $rows,
        ])
    </div>
    <div class="col-12 col-xl-4">
        @include('pages.admin.partials.side-cards', ['cards' => $sideCards])
    </div>
</div>
@endsection
