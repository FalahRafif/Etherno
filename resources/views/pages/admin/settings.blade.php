@extends('layouts.admin.admin')

@section('title', $title)

@section('content')
@php
    $actions = [
        ['label' => 'DP Verification', 'url' => panel_route('admin.payments.dp'), 'class' => 'btn btn-outline-primary btn-sm'],
        ['label' => 'Final Payment', 'url' => panel_route('admin.payments.final'), 'class' => 'btn btn-primary btn-sm'],
    ];

    $alerts = [
        ['class' => 'alert-info', 'text' => 'Halaman ini sebagai preview konfigurasi operasional. Fokus pada channel WhatsApp, rekening transfer, dan kebijakan pembayaran.'],
    ];

    $columns = ['Item', 'Nilai Saat Ini', 'Keterangan'];
    $rows = [
        ['Nomor WhatsApp Utama', '+62 812-xxxx-xxxx', 'Kanal konfirmasi pembayaran dan koordinasi utama'],
        ['Rekening Transfer', 'BCA 1234567890 a.n. Etherno', 'Digunakan untuk DP dan pelunasan manual'],
        ['Batas DP Expiration', '3 hari setelah approval', 'Jika lewat batas maka booking expired'],
        ['Policy Cancellation', 'DP non-refundable', 'Diterapkan untuk semua pembatalan booking'],
    ];

    $sideCards = [
        [
            'title' => 'Checklist Operasional',
            'items' => [
                ['label' => 'Template pesan WA approval', 'value' => 'Siap pakai', 'class' => 'text-success'],
                ['label' => 'Template reminder H-1', 'value' => 'Siap pakai', 'class' => 'text-success'],
                ['label' => 'Nomor rekening cadangan', 'value' => 'Opsional', 'class' => 'text-muted'],
                ['label' => 'SLA verifikasi pembayaran', 'value' => 'Maksimal hari yang sama', 'class' => 'text-primary'],
            ],
        ],
        [
            'title' => 'Kepatuhan README',
            'bullets' => [
                'Booking aktif hanya setelah DP verified.',
                'Final payment maksimal H-1 acara.',
                'Koordinasi lanjutan tetap via WhatsApp.',
            ],
        ],
    ];
@endphp

@include('pages.admin.partials.page-header', [
    'heading' => 'Settings',
    'summary' => 'Konfigurasi dasar operasional dan kebijakan agar flow admin tetap konsisten dengan kebutuhan bisnis.',
    'actions' => $actions,
])

@include('pages.admin.partials.alerts', ['alerts' => $alerts])

<div class="row g-3">
    <div class="col-12 col-xl-8">
        @include('pages.admin.partials.data-table', [
            'tableTitle' => 'Konfigurasi Operasional',
            'tableBadge' => 'Preview',
            'columns' => $columns,
            'rows' => $rows,
        ])
    </div>
    <div class="col-12 col-xl-4">
        @include('pages.admin.partials.side-cards', ['cards' => $sideCards])
    </div>
</div>
@endsection

