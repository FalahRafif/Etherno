@extends('layouts.admin.admin')

@section('title', $title)

@section('content')
@php
    $actions = [
        ['label' => 'Daftar Booking', 'url' => panel_route('admin.bookings.list'), 'class' => 'btn btn-outline-primary btn-sm'],
        ['label' => 'Booking Menunggu Review', 'url' => panel_route('admin.bookings.list', ['status' => 'BS_WAITING_APPROVAL']), 'class' => 'btn btn-outline-primary btn-sm'],
        ['label' => 'Booking Aktif', 'url' => panel_route('admin.bookings.list', ['status' => 'BS_CONFIRMED']), 'class' => 'btn btn-primary btn-sm'],
    ];

    $filters = $filters ?? ['status' => '', 'date_start' => '', 'date_end' => ''];
    $statusFilters = $statusFilters ?? [];
    $scheduleSummary = $scheduleSummary ?? [];
    $agendaReadiness = $agendaReadiness ?? ($upcomingBookings ?? []);
    $upcomingBookings = $upcomingBookings ?? [];
    $currentStatus = strtoupper(trim((string) ($filters['status'] ?? '')));

    $statusToneClass = [
        'primary' => 'bg-primary-transparent text-primary',
        'secondary' => 'bg-secondary-transparent text-secondary',
        'success' => 'bg-success-transparent text-success',
        'warning' => 'bg-warning-transparent text-warning',
        'danger' => 'bg-danger-transparent text-danger',
        'info' => 'bg-info-transparent text-info',
    ];
@endphp

@include('pages.admin.partials.page-header', [
    'heading' => 'Kalender Booking',
    'summary' => 'Pantau booking berdasarkan tanggal pengajuan atau tanggal acara, lalu buka detail jika perlu diproses.',
    'actions' => $actions,
])

<section class="calendar-workspace-summary mb-3">
    <div class="row g-3 align-items-stretch">
        <div class="col-12 col-xl-7">
            <div class="calendar-workspace-hero h-100">
                <span>Ringkasan Kalender</span>
                <h2>{{ $scheduleSummary['headline'] ?? 'Pantau jadwal booking.' }}</h2>
                <p>{{ $scheduleSummary['subline'] ?? 'Data kalender mengikuti filter status dan tanggal di bawah.' }}</p>
            </div>
        </div>
        <div class="col-12 col-xl-5">
            <div class="calendar-workspace-metrics h-100">
                @foreach(($scheduleSummary['metrics'] ?? []) as $metric)
                    @php
                        $tone = (string) ($metric['tone'] ?? 'primary');
                        $metricToneClass = $statusToneClass[$tone] ?? $statusToneClass['primary'];
                    @endphp
                    <div class="calendar-workspace-metric">
                        <span class="badge {{ $metricToneClass }}">{{ $metric['label'] ?? '-' }}</span>
                        <strong>{{ $metric['value'] ?? 0 }}</strong>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<div class="card custom-card mb-3">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h5 class="card-title mb-0">Filter Kalender</h5>
        <span id="booking_calendar_active_filter" class="badge bg-primary-transparent text-primary">Semua Status</span>
    </div>
    <div class="card-body">
        <div class="calendar-date-info mb-3">
            <span class="calendar-date-info-icon">i</span>
            <div>
                <strong>Catatan tanggal kalender</strong>
                <p class="mb-2">Warna label menunjukkan sumber tanggal yang dipakai:</p>
                <div class="calendar-date-info-legend">
                    <span><i class="calendar-legend-line"></i> Label Warna Abu-abu + garis warna: Booking di ajukan dan belum disetujui dan belum ada pembayaran (Data ditampilkan adalah tanggal pembuatan)</span>
                    <span><i class="calendar-legend-full"></i> Label Warna penuh status: Booking yang sudah disetujui dan memiliki pembayaran (data ditampilkan adalah tanggal pelaksanaan acara)</span>
                </div>
            </div>
        </div>
        <form id="booking_calendar_filter_form" data-events-url="{{ panel_route('admin.calendar.events', [], false) }}">
            <input type="hidden" id="calendar_status_filter" name="status" value="{{ $currentStatus }}">

            <div class="calendar-status-strip mb-3" id="booking_calendar_status_pills">
                @foreach($statusFilters as $statusFilter)
                    @php
                        $statusCode = strtoupper((string) ($statusFilter['code'] ?? ''));
                        $isActive = (bool) ($statusFilter['is_active'] ?? false);
                        $tone = (string) ($statusFilter['tone'] ?? 'secondary');
                        $tone = in_array($tone, ['primary', 'secondary', 'success', 'warning', 'danger', 'info', 'light', 'dark'], true) ? $tone : 'primary';
                        $countToneClass = $isActive ? 'bg-white text-' . ($tone === 'light' ? 'dark' : $tone) : ($statusToneClass[$tone] ?? $statusToneClass['secondary']);
                    @endphp
                    <button type="button" class="calendar-status-pill {{ $isActive ? 'is-active btn-' . $tone : 'btn-outline-' . $tone }}" data-status-code="{{ $statusCode }}">
                        <span>{{ $statusFilter['label'] }}</span>
                        <span class="badge {{ $countToneClass }}">{{ $statusFilter['count'] }}</span>
                    </button>
                @endforeach
            </div>

            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-4 col-xl-3">
                    <label class="form-label" for="calendar_date_start">Tanggal Mulai</label>
                    <input type="date" id="calendar_date_start" name="date_start" class="form-control" value="{{ $filters['date_start'] ?? '' }}">
                </div>
                <div class="col-12 col-md-4 col-xl-3">
                    <label class="form-label" for="calendar_date_end">Tanggal Akhir</label>
                    <input type="date" id="calendar_date_end" name="date_end" class="form-control" value="{{ $filters['date_end'] ?? '' }}">
                </div>
                <div class="col-12 col-md-4 col-xl-3 d-flex gap-2">
                    <button type="button" id="booking_calendar_apply_filter" class="btn btn-primary w-100">Terapkan Filter</button>
                    <button type="button" id="booking_calendar_reset_filter" class="btn btn-light w-100">Reset</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-xxl-9">
        <div class="card custom-card mb-0">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="card-title mb-0">Visualisasi Kalender Booking</h5>
            </div>
            <div class="card-body position-relative">
                <div id="booking_calendar_loading" class="booking-calendar-loading d-none">
                    <div class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></div>
                    <span>Memuat data booking...</span>
                </div>

                <div id="booking_calendar" class="booking-calendar"></div>
                <aside id="booking_calendar_preview" class="calendar-preview-panel d-none" aria-live="polite">
                    <div class="calendar-preview-header">
                        <div>
                            <span id="calendar_preview_risk" class="badge bg-primary-transparent text-primary">Preview</span>
                            <h6 id="calendar_preview_case" class="mb-0 mt-2">-</h6>
                        </div>
                        <button type="button" id="calendar_preview_close" class="btn btn-sm btn-light">Tutup</button>
                    </div>
                    <div class="calendar-preview-body">
                        <p class="calendar-preview-customer" id="calendar_preview_customer">-</p>
                        <dl>
                            <div><dt>Status</dt><dd id="calendar_preview_status">-</dd></div>
                            <div><dt>Kesiapan</dt><dd id="calendar_preview_readiness">-</dd></div>
                            <div><dt>Tampil di</dt><dd id="calendar_preview_source">-</dd></div>
                            <div><dt>Jadwal</dt><dd id="calendar_preview_schedule">-</dd></div>
                            <div><dt>Paket</dt><dd id="calendar_preview_package">-</dd></div>
                            <div><dt>Lokasi</dt><dd id="calendar_preview_location">-</dd></div>
                        </dl>
                        <a href="#" id="calendar_preview_detail" class="btn btn-primary w-100">Buka Detail</a>
                    </div>
                </aside>
            </div>
        </div>
    </div>

    <div class="col-12 col-xxl-3">
        <div class="card custom-card mb-0">
            <div class="card-header">
                <h5 class="card-title mb-0">Booking Terdekat</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0 booking-calendar-upcoming">
                    @forelse($agendaReadiness as $booking)
                        @php
                            $tone = (string) ($booking['tone'] ?? 'secondary');
                            $toneClass = $statusToneClass[$tone] ?? $statusToneClass['secondary'];
                        @endphp
                        <li class="booking-calendar-upcoming-item">
                            <a href="{{ $booking['url'] ?? '#' }}" class="calendar-agenda-link">
                                <div>
                                    <p class="mb-1 fw-semibold">{{ $booking['case_id'] }}</p>
                                    <p class="mb-1 text-muted small">{{ $booking['customer'] }}</p>
                                    <p class="mb-0 text-muted small">{{ $booking['date'] }} • {{ $booking['session'] }}</p>
                                </div>
                                <span class="text-end">
                                    <span class="badge {{ $toneClass }}">{{ $booking['readiness_label'] ?? ($booking['status_label'] ?? '-') }}</span>
                                    <em>{{ $booking['action_label'] ?? 'Detail' }}</em>
                                </span>
                            </a>
                        </li>
                    @empty
                        <li class="text-muted small">Belum ada booking terdekat pada filter saat ini.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/libs/fullcalendar/main.min.css') }}">
<style>
    .calendar-workspace-hero,
    .calendar-workspace-metrics {
        border: 1px solid rgba(30, 41, 59, 0.08);
        border-radius: 1.15rem;
    }
    .calendar-workspace-hero {
        height: 100%;
        padding: 1.35rem;
        background: linear-gradient(135deg, rgba(var(--primary-rgb), 0.14), rgba(255,255,255,0.86));
    }
    .calendar-workspace-hero span {
        display: inline-flex;
        margin-bottom: 0.7rem;
        color: rgb(var(--primary-rgb));
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .calendar-workspace-hero h2 {
        margin-bottom: 0.55rem;
        color: #1f2937;
        font-size: clamp(1.3rem, 2vw, 1.9rem);
        font-weight: 800;
    }
    .calendar-workspace-hero p {
        margin-bottom: 0;
        color: #64748b;
    }
    .calendar-workspace-metrics {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.8rem;
        height: 100%;
        padding: 1rem;
        background: #fff;
    }
    .calendar-workspace-metric {
        display: flex;
        min-height: 88px;
        flex-direction: column;
        justify-content: space-between;
        padding: 0.95rem;
        border-radius: 1rem;
        background: #f8fafc;
    }
    .calendar-workspace-metric strong {
        color: #111827;
        font-size: 1.8rem;
        line-height: 1;
    }
    .calendar-filter-section {
        padding: 1rem;
        border: 1px solid rgba(30, 41, 59, 0.08);
        border-radius: 1.15rem;
        background: #fff;
    }
    .calendar-filter-section-header {
        margin-bottom: 0.85rem;
    }
    .calendar-status-strip {
        display: flex;
        gap: 0.75rem;
        overflow-x: auto;
        padding-bottom: 0.35rem;
        scrollbar-width: thin;
    }
    .calendar-status-pill {
        min-width: 220px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.95rem 1rem;
        border: 1px solid currentColor;
        border-radius: 1rem;
        background: transparent;
        white-space: nowrap;
        flex: 0 0 auto;
        font-weight: 700;
    }
    .calendar-status-pill span:first-child {
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .calendar-status-pill .badge {
        flex-shrink: 0;
        font-size: 0.85rem;
    }
    .calendar-status-pill.is-active {
        background: rgb(var(--primary-rgb));
        border-color: rgb(var(--primary-rgb));
        color: #fff;
    }
    .calendar-status-pill:hover {
        filter: brightness(0.98);
    }
    .calendar-date-info {
        display: flex;
        gap: 0.75rem;
        padding: 0.85rem 1rem;
        border: 1px solid rgba(var(--primary-rgb), 0.18);
        border-radius: 0.95rem;
        background: rgba(var(--primary-rgb), 0.07);
        color: #475569;
    }
    .calendar-date-info-icon {
        display: inline-flex;
        width: 28px;
        height: 28px;
        flex: 0 0 auto;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: rgb(var(--primary-rgb));
        color: #fff;
        font-weight: 800;
    }
    .calendar-date-info strong {
        display: block;
        color: #1f2937;
    }
    .calendar-date-info-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 0.6rem 1rem;
        font-size: 0.82rem;
    }
    .calendar-date-info-legend span {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    .calendar-date-info-legend i {
        display: inline-block;
        width: 28px;
        height: 14px;
        border-radius: 0.35rem;
    }
    .calendar-legend-line {
        box-shadow: inset 6px 0 0 #f59e0b;
        background: #f1f5f9;
    }
    .calendar-legend-full {
        background: #38cab3;
    }
    .booking-calendar .fc .calendar-submission-date {
        border-color: #e2e8f0 !important;
        box-shadow: inset 6px 0 0 var(--calendar-status-color, #f59e0b) !important;
        padding-left: 0.45rem !important;
        opacity: 0.92;
    }
    .booking-calendar .fc .calendar-submission-date .fc-event-main {
        color: #334155 !important;
        padding-left: 0.25rem;
    }
    .booking-calendar .fc .calendar-event-date {
        border-left: 0 !important;
    }
    .booking-calendar {
        min-height: 680px;
    }
    .booking-calendar-loading {
        position: absolute;
        top: 1.2rem;
        right: 1.2rem;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #fff;
        border: 1px solid #e8e8f0;
        border-radius: 999px;
        padding: 0.35rem 0.65rem;
        font-size: 0.8rem;
        color: #4d5875;
    }
    .booking-calendar-upcoming {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        max-height: 500px;
        overflow: auto;
        padding-right: 0.25rem;
    }
    .booking-calendar-upcoming-item {
        border: 1px solid #ececf2;
        border-radius: 0.75rem;
        background: #fff;
    }
    .calendar-agenda-link {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.65rem 0.75rem;
        color: inherit;
        text-decoration: none;
    }
    .calendar-agenda-link:hover {
        color: inherit;
        text-decoration: none;
    }
    .calendar-agenda-link em {
        display: block;
        margin-top: 0.35rem;
        color: rgb(var(--primary-rgb));
        font-size: 0.76rem;
        font-style: normal;
        font-weight: 700;
    }
    .calendar-preview-panel {
        position: absolute;
        top: 1rem;
        right: 1rem;
        z-index: 5;
        width: min(360px, calc(100% - 2rem));
        overflow: hidden;
        border: 1px solid #e6e9f2;
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 22px 55px rgba(15, 23, 42, 0.16);
    }
    .calendar-preview-header {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem;
        border-bottom: 1px solid #edf0f6;
    }
    .calendar-preview-body {
        padding: 1rem;
    }
    .calendar-preview-customer {
        margin-bottom: 0.85rem;
        color: #64748b;
        font-weight: 700;
    }
    .calendar-preview-body dl {
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
        margin-bottom: 1rem;
    }
    .calendar-preview-body dl div {
        display: grid;
        grid-template-columns: 90px minmax(0, 1fr);
        gap: 0.75rem;
    }
    .calendar-preview-body dt {
        color: #64748b;
        font-size: 0.78rem;
        font-weight: 700;
    }
    .calendar-preview-body dd {
        margin-bottom: 0;
        color: #1f2937;
        font-weight: 700;
    }
    .booking-calendar .fc-toolbar-title {
        font-size: 1.35rem;
        font-weight: 600;
    }
    .booking-calendar .fc .fc-button-primary {
        background-color: #38cab3;
        border-color: #38cab3;
    }
    .booking-calendar .fc .fc-button-primary:hover,
    .booking-calendar .fc .fc-button-primary:focus {
        background-color: #2fb29e;
        border-color: #2fb29e;
    }
    .booking-calendar .fc .fc-event {
        cursor: pointer;
        border-radius: 0.35rem;
        border-width: 0;
        font-size: 0.72rem;
        padding: 0.12rem 0.32rem;
    }
    .booking-calendar .fc .fc-daygrid-event-dot {
        border-color: currentColor;
    }
    html[data-theme-mode="dark"] .calendar-workspace-hero,
    html[data-theme-mode="dark"] .calendar-workspace-metrics,
    html[data-theme-mode="dark"] .calendar-filter-section,
    html[data-theme-mode="dark"] .calendar-status-pill,
    html[data-theme-mode="dark"] .calendar-date-info,
    html[data-theme-mode="dark"] .booking-calendar-upcoming-item,
    html[data-theme-mode="dark"] .calendar-preview-panel,
    html[data-theme-mode="dark"] .calendar-preview-header {
        border-color: rgba(255, 255, 255, 0.08);
    }
    html[data-theme-mode="dark"] .calendar-workspace-hero {
        background: linear-gradient(135deg, rgba(var(--primary-rgb), 0.2), rgba(20, 24, 39, 0.94));
    }
    html[data-theme-mode="dark"] .calendar-workspace-metrics,
    html[data-theme-mode="dark"] .calendar-filter-section,
    html[data-theme-mode="dark"] .booking-calendar-upcoming-item,
    html[data-theme-mode="dark"] .calendar-preview-panel {
        background: rgba(255, 255, 255, 0.03);
    }
    html[data-theme-mode="dark"] .calendar-workspace-metric {
        background: rgba(255, 255, 255, 0.04);
    }
    html[data-theme-mode="dark"] .calendar-date-info {
        background: rgba(var(--primary-rgb), 0.12);
        color: #cbd5e1;
    }
    html[data-theme-mode="dark"] .calendar-legend-line {
        background: rgba(255, 255, 255, 0.08);
    }
    html[data-theme-mode="dark"] .booking-calendar .fc .calendar-submission-date {
        background: rgba(255, 255, 255, 0.08) !important;
        border-color: rgba(255, 255, 255, 0.12) !important;
        color: #e5e7eb !important;
    }
    html[data-theme-mode="dark"] .booking-calendar .fc .calendar-submission-date .fc-event-main {
        color: #e5e7eb !important;
    }
    html[data-theme-mode="dark"] .calendar-workspace-hero h2,
    html[data-theme-mode="dark"] .calendar-workspace-metric strong,
    html[data-theme-mode="dark"] .calendar-date-info strong,
    html[data-theme-mode="dark"] .calendar-preview-body dd {
        color: #f8fafc;
    }
    html[data-theme-mode="dark"] .calendar-workspace-hero p,
    html[data-theme-mode="dark"] .calendar-preview-customer,
    html[data-theme-mode="dark"] .calendar-preview-body dt {
        color: #cbd5e1;
    }
    @media (max-width: 991.98px) {
        .booking-calendar {
            min-height: 560px;
        }
        .booking-calendar .fc-toolbar {
            gap: 0.5rem;
        }
        .booking-calendar .fc-toolbar.fc-header-toolbar {
            margin-bottom: 0.9rem;
        }
        .calendar-preview-panel {
            position: static;
            width: 100%;
            margin-top: 1rem;
            box-shadow: none;
        }
    }
    @media (max-width: 767.98px) {
        .calendar-workspace-metrics {
            grid-template-columns: 1fr;
        }
        .calendar-status-pill {
            min-width: 260px;
        }
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('assets/libs/fullcalendar/main.min.js') }}"></script>
<script src="{{ asset('assets/libs/fullcalendar/locales-all.min.js') }}"></script>
<script src="{{ asset('assets/js/fullcalendar.js') }}"></script>
@php
    $calendarBookingScriptPath = public_path('assets/pages/admin/bookings/calendar-booking.js');
    $calendarBookingScriptVersion = file_exists($calendarBookingScriptPath) ? filemtime($calendarBookingScriptPath) : time();
@endphp
<script src="{{ asset('assets/pages/admin/bookings/calendar-booking.js') }}?v={{ $calendarBookingScriptVersion }}"></script>
@endpush
