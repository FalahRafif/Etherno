@extends('layouts.admin.admin')

@section('title', $title)

@section('content')
@php
    $actions = [
        ['label' => 'Cek Booking', 'url' => '#dashboard_action_center', 'class' => 'btn btn-primary btn-sm'],
        ['label' => 'Booking Menunggu Review', 'url' => panel_route('admin.bookings.list', ['status' => 'BS_WAITING_APPROVAL']), 'class' => 'btn btn-outline-primary btn-sm'],
        ['label' => 'Kalender & Slot', 'url' => panel_route('admin.calendar'), 'class' => 'btn btn-outline-primary btn-sm'],
    ];

    $alerts = [];
    $operationalSummary = $operationalSummary ?? [];
    $summaryMetrics = $operationalSummary['metrics'] ?? [];
    $actionCenter = $actionCenter ?? [];
    $alertCenter = $alertCenter ?? [];
    $todayTimeline = $todayTimeline ?? [];
    $upcomingReadiness = $upcomingReadiness ?? [];

    $toneClass = [
        'primary' => 'bg-primary-transparent text-primary',
        'secondary' => 'bg-secondary-transparent text-secondary',
        'success' => 'bg-success-transparent text-success',
        'warning' => 'bg-warning-transparent text-warning',
        'danger' => 'bg-danger-transparent text-danger',
        'info' => 'bg-info-transparent text-info',
        'light' => 'bg-light text-dark',
    ];

    $severityClass = [
        'critical' => 'ops-dashboard-severity-critical',
        'high' => 'ops-dashboard-severity-high',
        'medium' => 'ops-dashboard-severity-medium',
        'normal' => 'ops-dashboard-severity-normal',
    ];
@endphp

@include('pages.admin.partials.page-header', [
    'heading' => 'Dashboard Petugas',
    'summary' => 'Ringkasan booking yang perlu dicek, pembayaran, dan jadwal acara.',
    'actions' => $actions,
])

@include('pages.admin.partials.alerts', ['alerts' => $alerts])

<section class="ops-dashboard-summary mb-3">
    <div class="row g-3 align-items-stretch">
        <div class="col-12 col-xl-7">
            <div class="ops-dashboard-hero h-100">
                <span class="ops-dashboard-eyebrow">{{ $operationalSummary['eyebrow'] ?? 'Ringkasan Hari Ini' }}</span>
                <h2>{{ $operationalSummary['headline'] ?? 'Pantau prioritas operasional hari ini.' }}</h2>
                <p>{{ $operationalSummary['subline'] ?? 'Cek booking yang perlu diproses, lalu lanjutkan ke kalender jika perlu.' }}</p>
                <div class="d-flex flex-wrap gap-2 mt-3">
                    @if(!empty($operationalSummary['primary_action']))
                        <a href="{{ $operationalSummary['primary_action']['url'] }}" class="btn btn-primary">
                            {{ $operationalSummary['primary_action']['label'] }}
                        </a>
                    @endif
                    @foreach(($operationalSummary['secondary_actions'] ?? []) as $summaryAction)
                        <a href="{{ $summaryAction['url'] }}" class="btn btn-outline-primary">
                            {{ $summaryAction['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-5">
            <div class="ops-dashboard-metrics h-100">
                @foreach($summaryMetrics as $metric)
                    @php
                        $tone = (string) ($metric['tone'] ?? 'primary');
                        $metricToneClass = $toneClass[$tone] ?? $toneClass['primary'];
                    @endphp
                    <div class="ops-dashboard-metric">
                        <span class="badge {{ $metricToneClass }}">{{ $metric['label'] ?? '-' }}</span>
                        <strong>{{ $metric['value'] ?? 0 }}</strong>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<div class="row g-3 align-items-start">
    <div class="col-12 col-xxl-8">
        <div class="card custom-card ops-dashboard-card mb-3" id="dashboard_action_center">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="card-title mb-1">Booking Perlu Dicek</h5>
                    <p class="text-muted small mb-0">Daftar booking yang perlu diproses lebih dulu.</p>
                </div>
                        <span class="badge bg-primary-transparent text-primary">{{ count($actionCenter) }} booking</span>
            </div>
            <div class="card-body">
                <div class="ops-dashboard-action-list">
                    @forelse($actionCenter as $item)
                        @php
                            $severity = (string) ($item['severity'] ?? 'normal');
                            $severityItemClass = $severityClass[$severity] ?? $severityClass['normal'];
                        @endphp
                        <article class="ops-dashboard-action-item {{ $severityItemClass }}">
                            <div class="ops-dashboard-action-main">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                    <span class="ops-dashboard-severity-label">{{ $item['severity_label'] ?? 'Normal' }}</span>
                                    <span class="badge bg-light text-dark">{{ $item['status_label'] ?? '-' }}</span>
                                    <span class="text-muted small">{{ $item['due_label'] ?? '-' }}</span>
                                </div>
                                <h6 class="mb-1">{{ $item['title'] ?? 'Tindak lanjut booking' }}</h6>
                                <p class="mb-2">{{ $item['reason'] ?? '-' }}</p>
                                <div class="ops-dashboard-action-meta">
                                    <strong>{{ $item['case_id'] ?? '-' }}</strong>
                                    <span>{{ $item['customer'] ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="ops-dashboard-action-cta">
                                <a href="{{ $item['detail_url'] ?? '#' }}" class="btn btn-primary btn-sm">{{ $item['action_label'] ?? 'Buka Detail' }}</a>
                                <a href="{{ $item['list_url'] ?? panel_route('admin.bookings.list') }}" class="btn btn-light btn-sm">Lihat Sejenis</a>
                            </div>
                        </article>
                    @empty
                        <div class="ops-dashboard-empty">
                            <h6 class="mb-1">Belum ada booking yang perlu dicek segera.</h6>
                            <p class="text-muted mb-3">Lihat kalender jika ingin mengecek jadwal acara.</p>
                            <a href="{{ panel_route('admin.calendar') }}" class="btn btn-outline-primary btn-sm">Buka Kalender</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="card custom-card ops-dashboard-card mb-3">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="card-title mb-1">Agenda Hari Ini</h5>
                    <p class="text-muted small mb-0">Jadwal acara dan booking baru hari ini.</p>
                </div>
            </div>
            <div class="card-body">
                <div class="ops-dashboard-timeline">
                    @forelse($todayTimeline as $item)
                        @php
                            $tone = (string) ($item['tone'] ?? 'primary');
                            $timelineToneClass = $toneClass[$tone] ?? $toneClass['primary'];
                        @endphp
                        <a href="{{ $item['url'] ?? '#' }}" class="ops-dashboard-timeline-item">
                            <span class="ops-dashboard-timeline-marker {{ $timelineToneClass }}"></span>
                            <span class="ops-dashboard-timeline-time">{{ $item['time_label'] ?? '-' }}</span>
                            <span class="ops-dashboard-timeline-body">
                                <strong>{{ $item['title'] ?? '-' }}</strong>
                                <small>{{ $item['description'] ?? '-' }}</small>
                            </span>
                            <span class="ops-dashboard-timeline-meta">{{ $item['meta'] ?? '' }}</span>
                        </a>
                    @empty
                        <div class="ops-dashboard-empty text-start">
                            <h6 class="mb-1">Belum ada agenda khusus hari ini.</h6>
                            <p class="text-muted mb-0">Cek kalender untuk jadwal berikutnya.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xxl-4">
        <div class="card custom-card ops-dashboard-card mb-3">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="card-title mb-1">Catatan</h5>
                    <p class="text-muted small mb-0">Hal yang perlu diperhatikan.</p>
                </div>
                <span class="badge bg-danger-transparent text-danger">{{ count($alertCenter) }} catatan</span>
            </div>
            <div class="card-body">
                <div class="ops-dashboard-alert-list">
                    @forelse($alertCenter as $alert)
                        @php
                            $tone = (string) ($alert['severity'] ?? 'info');
                            $alertToneClass = $toneClass[$tone] ?? $toneClass['info'];
                        @endphp
                        <a href="{{ $alert['url'] ?? '#' }}" class="ops-dashboard-alert-item">
                            <span class="ops-dashboard-alert-icon {{ $alertToneClass }}">!</span>
                            <span>
                                <strong>{{ $alert['title'] ?? '-' }}</strong>
                                <small>{{ $alert['description'] ?? '-' }}</small>
                                <em>{{ $alert['action_label'] ?? 'Lihat Detail' }}</em>
                            </span>
                        </a>
                    @empty
                        <div class="ops-dashboard-empty text-start">
                            <h6 class="mb-1">Tidak ada alert aktif.</h6>
                            <p class="text-muted mb-0">Tidak ada catatan penting saat ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="card custom-card ops-dashboard-card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-1">Kesiapan Acara 7 Hari</h5>
                <p class="text-muted small mb-0">Agenda dekat dengan status kesiapan operasional.</p>
            </div>
            <div class="card-body">
                <div class="ops-dashboard-readiness-list">
                    @forelse($upcomingReadiness as $booking)
                        @php
                            $tone = (string) ($booking['tone'] ?? 'secondary');
                            $readinessToneClass = $toneClass[$tone] ?? $toneClass['secondary'];
                        @endphp
                        <a href="{{ $booking['url'] ?? '#' }}" class="ops-dashboard-readiness-item">
                            <span>
                                <strong>{{ $booking['date_label'] ?? '-' }} • {{ $booking['session'] ?? '-' }}</strong>
                                <small>{{ $booking['case_id'] ?? '-' }} - {{ $booking['customer'] ?? '-' }}</small>
                                <small>{{ $booking['status_label'] ?? '-' }}</small>
                            </span>
                            <span class="text-end">
                                <span class="badge {{ $readinessToneClass }}">{{ $booking['readiness_label'] ?? '-' }}</span>
                                <em>{{ $booking['action_label'] ?? 'Detail' }}</em>
                            </span>
                        </a>
                    @empty
                        <div class="ops-dashboard-empty text-start">
                            <h6 class="mb-1">Tidak ada agenda dekat.</h6>
                            <p class="text-muted mb-0">Belum ada booking aktif dalam 7 hari ke depan.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.ops-dashboard-hero,
.ops-dashboard-metrics,
.ops-dashboard-card {
    border: 1px solid rgba(30, 41, 59, 0.08);
}

.ops-dashboard-hero {
    padding: 1.35rem;
    border-radius: 1.15rem;
    background: linear-gradient(135deg, rgba(var(--primary-rgb), 0.14), rgba(255,255,255,0.86));
    box-shadow: 0 12px 28px rgba(15, 23, 42, 0.05);
}

.ops-dashboard-eyebrow {
    display: inline-flex;
    margin-bottom: 0.7rem;
    color: rgb(var(--primary-rgb));
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.ops-dashboard-hero h2 {
    margin-bottom: 0.55rem;
    font-size: clamp(1.35rem, 2.2vw, 2.1rem);
    font-weight: 800;
    color: #1f2937;
}

.ops-dashboard-hero p {
    max-width: 720px;
    margin-bottom: 0;
    color: #64748b;
    font-size: 0.98rem;
}

.ops-dashboard-metrics {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.85rem;
    padding: 1rem;
    border-radius: 1.15rem;
    background: #fff;
}

.ops-dashboard-metric {
    display: flex;
    min-height: 96px;
    flex-direction: column;
    justify-content: space-between;
    padding: 1rem;
    border-radius: 1rem;
    background: #f8fafc;
}

.ops-dashboard-metric strong {
    color: #111827;
    font-size: 2rem;
    line-height: 1;
}

.ops-dashboard-action-list,
.ops-dashboard-alert-list,
.ops-dashboard-readiness-list,
.ops-dashboard-timeline {
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
}

.ops-dashboard-action-item {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    gap: 1rem;
    padding: 1rem;
    border: 1px solid #edf0f6;
    border-left-width: 5px;
    border-radius: 1rem;
    background: #fff;
}

.ops-dashboard-severity-critical { border-left-color: #e6533c; }
.ops-dashboard-severity-high { border-left-color: #f59e0b; }
.ops-dashboard-severity-medium { border-left-color: #38cab3; }
.ops-dashboard-severity-normal { border-left-color: #94a3b8; }

.ops-dashboard-severity-label {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.6rem;
    border-radius: 999px;
    background: rgba(230, 83, 60, 0.1);
    color: #e6533c;
    font-size: 0.72rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.ops-dashboard-action-main h6,
.ops-dashboard-alert-item strong,
.ops-dashboard-readiness-item strong,
.ops-dashboard-timeline-body strong {
    color: #1f2937;
}

.ops-dashboard-action-main p {
    color: #64748b;
}

.ops-dashboard-action-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.45rem 0.75rem;
    color: #475569;
    font-size: 0.86rem;
}

.ops-dashboard-action-cta {
    display: flex;
    min-width: 150px;
    flex-direction: column;
    justify-content: center;
    gap: 0.45rem;
}

.ops-dashboard-alert-item,
.ops-dashboard-readiness-item,
.ops-dashboard-timeline-item {
    display: flex;
    gap: 0.75rem;
    padding: 0.85rem;
    border: 1px solid #edf0f6;
    border-radius: 0.95rem;
    background: #fff;
    color: inherit;
    text-decoration: none;
}

.ops-dashboard-alert-item:hover,
.ops-dashboard-readiness-item:hover,
.ops-dashboard-timeline-item:hover {
    border-color: rgba(var(--primary-rgb), 0.35);
    color: inherit;
    text-decoration: none;
}

.ops-dashboard-alert-icon {
    display: inline-flex;
    width: 34px;
    height: 34px;
    flex: 0 0 auto;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-weight: 800;
}

.ops-dashboard-alert-item span:last-child,
.ops-dashboard-readiness-item > span:first-child,
.ops-dashboard-timeline-body {
    display: flex;
    min-width: 0;
    flex: 1;
    flex-direction: column;
    gap: 0.2rem;
}

.ops-dashboard-alert-item small,
.ops-dashboard-readiness-item small,
.ops-dashboard-timeline-body small {
    color: #64748b;
}

.ops-dashboard-alert-item em,
.ops-dashboard-readiness-item em {
    display: block;
    margin-top: 0.35rem;
    color: rgb(var(--primary-rgb));
    font-size: 0.78rem;
    font-style: normal;
    font-weight: 700;
}

.ops-dashboard-readiness-item {
    justify-content: space-between;
}

.ops-dashboard-timeline-item {
    align-items: center;
}

.ops-dashboard-timeline-marker {
    width: 10px;
    height: 10px;
    flex: 0 0 auto;
    border-radius: 50%;
}

.ops-dashboard-timeline-time {
    width: 110px;
    flex: 0 0 auto;
    color: #475569;
    font-weight: 700;
}

.ops-dashboard-timeline-meta {
    flex: 0 0 auto;
    color: #64748b;
    font-size: 0.78rem;
}

.ops-dashboard-empty {
    padding: 1rem;
    border: 1px dashed #d9dee9;
    border-radius: 1rem;
    background: #f8fafc;
    text-align: center;
}

html[data-theme-mode="dark"] .ops-dashboard-hero,
html[data-theme-mode="dark"] .ops-dashboard-metrics,
html[data-theme-mode="dark"] .ops-dashboard-card,
html[data-theme-mode="dark"] .ops-dashboard-action-item,
html[data-theme-mode="dark"] .ops-dashboard-alert-item,
html[data-theme-mode="dark"] .ops-dashboard-readiness-item,
html[data-theme-mode="dark"] .ops-dashboard-timeline-item,
html[data-theme-mode="dark"] .ops-dashboard-empty {
    border-color: rgba(255, 255, 255, 0.08);
}

html[data-theme-mode="dark"] .ops-dashboard-hero {
    background: linear-gradient(135deg, rgba(var(--primary-rgb), 0.2), rgba(20, 24, 39, 0.94));
    box-shadow: none;
}

html[data-theme-mode="dark"] .ops-dashboard-metrics,
html[data-theme-mode="dark"] .ops-dashboard-action-item,
html[data-theme-mode="dark"] .ops-dashboard-alert-item,
html[data-theme-mode="dark"] .ops-dashboard-readiness-item,
html[data-theme-mode="dark"] .ops-dashboard-timeline-item {
    background: rgba(255, 255, 255, 0.03);
}

html[data-theme-mode="dark"] .ops-dashboard-metric,
html[data-theme-mode="dark"] .ops-dashboard-empty {
    background: rgba(255, 255, 255, 0.04);
}

html[data-theme-mode="dark"] .ops-dashboard-hero h2,
html[data-theme-mode="dark"] .ops-dashboard-metric strong,
html[data-theme-mode="dark"] .ops-dashboard-action-main h6,
html[data-theme-mode="dark"] .ops-dashboard-alert-item strong,
html[data-theme-mode="dark"] .ops-dashboard-readiness-item strong,
html[data-theme-mode="dark"] .ops-dashboard-timeline-body strong {
    color: #f8fafc;
}

html[data-theme-mode="dark"] .ops-dashboard-hero p,
html[data-theme-mode="dark"] .ops-dashboard-action-main p,
html[data-theme-mode="dark"] .ops-dashboard-action-meta,
html[data-theme-mode="dark"] .ops-dashboard-alert-item small,
html[data-theme-mode="dark"] .ops-dashboard-readiness-item small,
html[data-theme-mode="dark"] .ops-dashboard-timeline-body small,
html[data-theme-mode="dark"] .ops-dashboard-timeline-time,
html[data-theme-mode="dark"] .ops-dashboard-timeline-meta {
    color: #cbd5e1;
}

@media (max-width: 767.98px) {
    .ops-dashboard-metrics {
        grid-template-columns: 1fr;
    }

    .ops-dashboard-action-item {
        grid-template-columns: 1fr;
    }

    .ops-dashboard-action-cta {
        min-width: 0;
    }

    .ops-dashboard-timeline-item {
        align-items: flex-start;
        flex-wrap: wrap;
    }

    .ops-dashboard-timeline-time {
        width: auto;
    }

    .ops-dashboard-timeline-meta {
        width: 100%;
        padding-left: 1.4rem;
    }
}
</style>
@endpush
