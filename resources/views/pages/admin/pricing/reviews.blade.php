@extends('layouts.admin.admin')

@section('title', $title)

@section('content')
@php
    $actions = [
        ['label' => 'Aturan Harga Lokasi', 'url' => panel_route('admin.location.rules'), 'class' => 'btn btn-outline-primary btn-sm'],
        ['label' => 'Packages', 'url' => panel_route('admin.packages'), 'class' => 'btn btn-primary btn-sm'],
    ];

    $alerts = [
        [
            'class' => 'alert-info',
            'text' => 'Aturan display: jangan tampilkan angka final di tahap awal. Gunakan base price + estimasi, lalu final price dikirim setelah cek lokasi.',
        ],
    ];

    $stats = [
        ['label' => 'Perlu Final Price', 'value' => '8', 'hint' => 'Sudah DP verified', 'tone' => 'warning'],
        ['label' => 'Tambahan Lokasi', 'value' => '5', 'hint' => 'Transport / akomodasi', 'tone' => 'info'],
        ['label' => 'Estimasi Overtime', 'value' => '2', 'hint' => 'Durasi > 8 jam', 'tone' => 'danger'],
        ['label' => 'Final Sent via WA', 'value' => '11', 'hint' => 'Sudah dikirim ke customer', 'tone' => 'success'],
    ];

    $columns = ['Kode', 'Paket Dasar', 'Kota', 'Estimasi Tambahan', 'Harga Final', 'Status', 'Aksi'];
    $rows = [
        [
            'ETH-2026-014',
            'Andalan (IDR 12.000.000)',
            'Bandung',
            ['type' => 'stack', 'primary' => 'Ringan', 'secondary' => 'Transport lokal'],
            'IDR 12.800.000',
            ['type' => 'badge', 'tone' => 'success', 'label' => 'final_sent'],
            ['type' => 'link', 'label' => 'Detail', 'url' => panel_route('admin.bookings.detail', ['booking' => 'eth-2026-014'])],
        ],
        [
            'ETH-2026-027',
            'Mewah (IDR 20.000.000)',
            'Bali',
            ['type' => 'stack', 'primary' => 'Custom', 'secondary' => 'Transport + akomodasi'],
            'Menunggu perhitungan',
            ['type' => 'badge', 'tone' => 'warning', 'label' => 'in_review'],
            ['type' => 'link', 'label' => 'Hitung Final', 'url' => panel_route('admin.location.rules')],
        ],
        [
            'ETH-2026-028',
            'Intim (IDR 6.000.000)',
            'Yogyakarta',
            ['type' => 'stack', 'primary' => 'Sedang', 'secondary' => 'Transport antar kota'],
            'IDR 7.200.000',
            ['type' => 'badge', 'tone' => 'info', 'label' => 'ready_to_send'],
            ['type' => 'link', 'label' => 'Kirim ke WA', 'url' => panel_route('admin.bookings.detail', ['booking' => 'eth-2026-028'])],
        ],
    ];

    $sideCards = [
        [
            'title' => 'Komponen Biaya Tambahan',
            'items' => [
                ['label' => 'Transport', 'value' => 'Berdasarkan kategori kota'],
                ['label' => 'Akomodasi', 'value' => 'Khusus luar kota/pulau'],
                ['label' => 'Overtime', 'value' => 'Jika durasi lebih dari 8 jam'],
            ],
        ],
        [
            'title' => 'Note Transparansi Wajib',
            'lines' => [
                'Biaya tambahan (transport, akomodasi, dll) akan disesuaikan berdasarkan lokasi dan dikonfirmasi setelah pengecekan.',
            ],
        ],
    ];
@endphp

@include('pages.admin.partials.page-header', [
    'heading' => 'Pricing Review',
    'summary' => 'Perhitungan harga final pasca DP dengan breakdown biaya tambahan sesuai kategori lokasi.',
    'actions' => $actions,
])

@include('pages.admin.partials.alerts', ['alerts' => $alerts])
@include('pages.admin.partials.stats-grid', ['stats' => $stats])

<div class="row g-3">
    <div class="col-12 col-xl-8">
        @include('pages.admin.partials.data-table', [
            'tableTitle' => 'Post-DP Final Pricing',
            'tableBadge' => 'After DP Verified',
            'columns' => $columns,
            'rows' => $rows,
        ])
    </div>
    <div class="col-12 col-xl-4">
        @include('pages.admin.partials.side-cards', ['cards' => $sideCards])
    </div>
</div>
@endsection
