@extends('layouts.admin.admin')

@section('title', $title)

@section('content')
@php
    $actions = [
        ['label' => 'Pricing Review', 'url' => route('admin.pricing.reviews'), 'class' => 'btn btn-primary btn-sm'],
    ];

    $alerts = [
        ['class' => 'alert-warning', 'text' => 'Perhitungan tambahan berbasis kota, bukan kilometer. Area luar pulau wajib hitung transport + akomodasi secara custom.'],
    ];

    $columns = ['Kategori', 'Cakupan Kota', 'Level Tambahan', 'Komponen Biaya', 'Catatan'];
    $rows = [
        ['Jabodetabek / Bandung', 'Jakarta, Bogor, Depok, Tangerang, Bekasi, Bandung', ['type' => 'badge', 'tone' => 'success', 'label' => 'Ringan'], 'Transport lokal', 'Range kecil, tetap konfirmasi admin'],
        ['Luar Kota (Jawa)', 'Yogyakarta, Semarang, Surabaya, Malang, dll', ['type' => 'badge', 'tone' => 'warning', 'label' => 'Sedang'], 'Transport antar kota', 'Hitung sesuai kebutuhan operasional'],
        ['Luar Pulau', 'Bali, Sumatera, Kalimantan, Sulawesi, dll', ['type' => 'badge', 'tone' => 'danger', 'label' => 'Custom'], 'Transport + Akomodasi', 'Final ditentukan setelah pengecekan detail'],
    ];

    $sideCards = [
        [
            'title' => 'Aturan Transparansi',
            'bullets' => [
                'Tampilkan estimasi/range di tahap awal.',
                'Jangan tampilkan angka final sebelum pengecekan.',
                'Sertakan note biaya tambahan pada halaman publik.',
            ],
        ],
        [
            'title' => 'Note Wajib Public',
            'lines' => [
                'Biaya tambahan (transport, akomodasi, dll) akan disesuaikan berdasarkan lokasi dan dikonfirmasi setelah pengecekan.',
            ],
        ],
    ];
@endphp

@include('pages.admin.partials.page-header', [
    'heading' => 'Location Rules',
    'summary' => 'Klasifikasi lokasi untuk estimasi biaya tambahan agar pricing lebih konsisten dan transparan.',
    'actions' => $actions,
])

@include('pages.admin.partials.alerts', ['alerts' => $alerts])

<div class="row g-3">
    <div class="col-12 col-xl-8">
        @include('pages.admin.partials.data-table', [
            'tableTitle' => 'Kategori Lokasi',
            'tableBadge' => 'City-Based Rule',
            'columns' => $columns,
            'rows' => $rows,
        ])
    </div>
    <div class="col-12 col-xl-4">
        @include('pages.admin.partials.side-cards', ['cards' => $sideCards])
    </div>
</div>
@endsection
