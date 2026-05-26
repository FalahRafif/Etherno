@extends('layouts.admin.admin')

@section('title', $title)

@section('content')
@php
    $actions = [
        ['label' => 'Calendar & Slots', 'url' => route('admin.calendar'), 'class' => 'btn btn-outline-primary btn-sm'],
        ['label' => 'Bookings Active', 'url' => route('admin.bookings.active'), 'class' => 'btn btn-primary btn-sm'],
    ];

    $alerts = [
        ['class' => 'alert-info', 'text' => 'Reschedule diproses manual via WhatsApp dan hanya bisa diajukan maksimal 2 minggu sebelum tanggal acara.'],
    ];

    $columns = ['Kode', 'Tanggal Lama', 'Tanggal Baru', 'Sisa Hari ke Acara', 'Ketersediaan Slot', 'Status', 'Aksi'];
    $rows = [
        [
            'ETH-2026-011',
            '2026-06-30',
            '2026-07-12',
            '24 hari',
            ['type' => 'badge', 'tone' => 'success', 'label' => 'Tersedia'],
            ['type' => 'badge', 'tone' => 'warning', 'label' => 'reschedule_requested'],
            ['type' => 'link', 'label' => 'Review', 'url' => route('admin.bookings.detail', ['booking' => 'eth-2026-011'])],
        ],
        [
            'ETH-2026-009',
            '2026-06-18',
            '2026-06-19',
            '8 hari',
            ['type' => 'badge', 'tone' => 'danger', 'label' => 'Perlu eskalasi'],
            ['type' => 'badge', 'tone' => 'danger', 'label' => 'over_policy_window'],
            ['type' => 'link', 'label' => 'Escalate', 'url' => route('admin.bookings.detail', ['booking' => 'eth-2026-009'])],
        ],
    ];

    $sideCards = [
        [
            'title' => 'Policy Reschedule',
            'items' => [
                ['label' => 'Batas request', 'value' => 'Maksimal 2 minggu sebelum acara'],
                ['label' => 'Penentu approval', 'value' => 'Ketersediaan slot'],
                ['label' => 'Channel utama', 'value' => 'WhatsApp'],
                ['label' => 'Sistem', 'value' => 'Pencatatan status & histori'],
            ],
        ],
    ];
@endphp

@include('pages.admin.partials.page-header', [
    'heading' => 'Reschedule Requests',
    'summary' => 'Review permintaan perubahan jadwal berdasarkan policy dan kapasitas slot.',
    'actions' => $actions,
])

@include('pages.admin.partials.alerts', ['alerts' => $alerts])

<div class="row g-3">
    <div class="col-12 col-xl-8">
        @include('pages.admin.partials.data-table', [
            'tableTitle' => 'Antrian Reschedule',
            'tableBadge' => 'Manual Approval',
            'columns' => $columns,
            'rows' => $rows,
        ])
    </div>
    <div class="col-12 col-xl-4">
        @include('pages.admin.partials.side-cards', ['cards' => $sideCards])
    </div>
</div>
@endsection
