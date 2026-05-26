@extends('layouts.admin.admin')

@section('title', $title)

@section('content')
@php
    $actions = [
        ['label' => 'Approve Booking', 'url' => route('admin.bookings.requests'), 'class' => 'btn btn-success btn-sm'],
        ['label' => 'Verifikasi DP', 'url' => route('admin.payments.dp'), 'class' => 'btn btn-primary btn-sm'],
        ['label' => 'Set Final Price', 'url' => route('admin.pricing.reviews'), 'class' => 'btn btn-outline-primary btn-sm'],
    ];

    $alerts = [
        ['class' => 'alert-info', 'text' => 'Preview booking code: ' . ($bookingCode ?? 'ETH-XXXX')],
    ];

    $stats = [
        ['label' => 'Booking Status', 'value' => 'under_review', 'hint' => 'Belum aktif sebelum DP verified', 'tone' => 'warning'],
        ['label' => 'Payment Status', 'value' => 'dp_waiting_payment', 'hint' => 'Menunggu transfer DP', 'tone' => 'info'],
        ['label' => 'Tanggal & Sesi', 'value' => '2026-07-12 / Pagi-Siang', 'hint' => 'Slot belum terkunci', 'tone' => 'primary'],
        ['label' => 'Jenis Acara', 'value' => 'Wedding', 'hint' => 'Skema DP 15%', 'tone' => 'success'],
    ];

    $timelineColumns = ['Tahap', 'Status', 'Catatan', 'Waktu Update'];
    $timelineRows = [
        ['Submit Form', ['type' => 'badge', 'tone' => 'success', 'label' => 'done'], 'Customer kirim data lengkap termasuk lokasi dan detail acara.', '2026-06-01 08:14'],
        ['Admin Review', ['type' => 'badge', 'tone' => 'warning', 'label' => 'in_progress'], 'Validasi slot, cek pin maps, cek kesesuaian paket.', '2026-06-01 09:03'],
        ['DP Confirmation', ['type' => 'badge', 'tone' => 'light', 'label' => 'pending'], 'Konfirmasi via WhatsApp belum diterima.', '-'],
        ['Final Payment', ['type' => 'badge', 'tone' => 'light', 'label' => 'pending'], 'Akan diproses setelah harga final dikirim.', '-'],
    ];

    $sideCards = [
        [
            'title' => 'Policy Reminder',
            'bullets' => [
                'Booking fix hanya setelah DP terverifikasi.',
                'Batas pembayaran DP maksimal 3 hari setelah approval.',
                'Harga final disampaikan setelah pengecekan lokasi.',
                'Komunikasi lanjut tetap via WhatsApp.',
            ],
        ],
        [
            'title' => 'Aksi Cepat',
            'actions' => [
                ['label' => 'Buka Queue DP', 'url' => route('admin.payments.dp'), 'class' => 'btn btn-outline-primary btn-sm'],
                ['label' => 'Buka Calendar', 'url' => route('admin.calendar'), 'class' => 'btn btn-outline-primary btn-sm'],
            ],
        ],
    ];
@endphp

@include('pages.admin.partials.page-header', [
    'heading' => 'Booking Detail',
    'summary' => 'Detail operasional untuk approval, verifikasi DP, final pricing, dan koordinasi booking.',
    'actions' => $actions,
])

@include('pages.admin.partials.alerts', ['alerts' => $alerts])
@include('pages.admin.partials.stats-grid', ['stats' => $stats])

<div class="row g-3 mb-3">
    <div class="col-12 col-xl-6">
        <div class="card custom-card mb-0 h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">Data Customer</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">Nama</span><span>Nadia Putri</span></li>
                    <li class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">WhatsApp</span><span>08xxxxxxxxxx</span></li>
                    <li class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">Jenis Acara</span><span>Wedding</span></li>
                    <li class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">Paket</span><span>Andalan</span></li>
                    <li class="d-flex justify-content-between py-2"><span class="text-muted">Detail Acara</span><span class="text-end">Akad + resepsi keluarga inti</span></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-6">
        <div class="card custom-card mb-0 h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">Data Lokasi & Slot</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">Tanggal</span><span>2026-07-12</span></li>
                    <li class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">Sesi</span><span>Pagi-Siang</span></li>
                    <li class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">Kota</span><span>Bandung</span></li>
                    <li class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">Pin Google Maps</span><span class="text-success">Tersedia</span></li>
                    <li class="py-2">
                        <span class="text-muted d-block mb-1">Catatan Admin</span>
                        <small class="text-muted">Lokasi dalam kategori tambahan ringan. Final price dihitung setelah DP verified.</small>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-xl-8">
        @include('pages.admin.partials.data-table', [
            'tableTitle' => 'Timeline Proses',
            'tableBadge' => 'Booking Lifecycle',
            'columns' => $timelineColumns,
            'rows' => $timelineRows,
        ])
    </div>
    <div class="col-12 col-xl-4">
        @include('pages.admin.partials.side-cards', ['cards' => $sideCards])
    </div>
</div>
@endsection
