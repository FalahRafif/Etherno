@extends('layouts.admin.admin')

@section('title', $title)

@section('content')
@php
    $actions = [
        ['label' => 'Booking Menunggu Review', 'url' => panel_route('admin.bookings.list', ['status' => 'BS_WAITING_APPROVAL']), 'class' => 'btn btn-outline-primary btn-sm'],
        ['label' => 'Booking Aktif', 'url' => panel_route('admin.bookings.list', ['status' => 'BS_CONFIRMED']), 'class' => 'btn btn-outline-primary btn-sm'],
        ['label' => 'Kalender & Slot', 'url' => panel_route('admin.calendar'), 'class' => 'btn btn-primary btn-sm'],
    ];
    $columns = ['Case ID', 'Customer', 'Tanggal Pengajuan', 'Tanggal Acara', 'Sesi', 'Paket', 'Lokasi', 'Status', 'Aksi'];
    $stats = $stats ?? [];
    $rows = $rows ?? [];
    $statusFilters = $statusFilters ?? [];
    $filters = $filters ?? [];
    $pagination = $pagination ?? null;
    $totalCount = $totalCount ?? 0;
    $filteredCount = $filteredCount ?? 0;
    $currentStatus = strtoupper(trim((string) ($filters['status'] ?? '')));
    $queryFilters = array_filter($filters, static fn ($value) => $value !== null && $value !== '');
@endphp

@include('pages.admin.partials.page-header', [
    'heading' => 'Daftar Booking',
    'summary' => 'Daftar seluruh booking dengan filter status, case ID, dan rentang tanggal.',
    'actions' => $actions,
])

@include('pages.admin.partials.stats-grid', ['stats' => $stats])

<div class="card custom-card mb-3">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h5 class="card-title mb-0">Filter Booking</h5>
        <span class="badge bg-primary-transparent text-primary">Menampilkan {{ $filteredCount }} dari {{ $totalCount }} booking</span>
    </div>
    <div class="card-body">
        <div class="booking-filter-strip mb-3">
            @foreach ($statusFilters as $filter)
                @php
                    $isActive = $filter['is_active'] ?? false;
                    $filterCode = trim((string) ($filter['code'] ?? ''));
                    $tone = (string) ($filter['tone'] ?? 'primary');
                    $tone = in_array($tone, ['primary', 'secondary', 'success', 'warning', 'danger', 'info', 'light', 'dark'], true)
                        ? $tone
                        : 'primary';
                    $toneBadgeClass = match ($tone) {
                        'light' => 'bg-light text-dark',
                        'dark' => 'bg-dark text-white',
                        default => 'bg-' . $tone . '-transparent text-' . $tone,
                    };
                    $activeBadgeClass = match ($tone) {
                        'light', 'dark' => 'bg-white text-dark',
                        default => 'bg-white text-' . $tone,
                    };
                    $badgeClass = $isActive ? $activeBadgeClass : $toneBadgeClass;
                    $baseFilters = $queryFilters;
                    unset($baseFilters['status']);
                    $targetFilters = $filterCode !== '' ? array_merge($baseFilters, ['status' => $filterCode]) : $baseFilters;
                @endphp
                <a href="{{ panel_route('admin.bookings.list', $targetFilters) }}" class="booking-filter-pill {{ $isActive ? 'is-active btn-' . $tone : 'btn-outline-' . $tone }}">
                    <span class="booking-filter-pill__label">{{ $filter['label'] ?? '-' }}</span>
                    <span class="badge {{ $badgeClass }} booking-filter-pill__count">{{ $filter['count'] ?? 0 }}</span>
                </a>
            @endforeach
        </div>

        <form method="GET" action="{{ panel_route('admin.bookings.list') }}" class="row g-3 align-items-end" id="booking_filter_form">
            <input type="hidden" name="status" value="{{ $currentStatus }}">
            <div class="col-12 col-md-4 col-xl-3">
                <label for="case_id" class="form-label">Case ID</label>
                <input type="text" id="case_id" name="case_id" class="form-control" value="{{ $filters['case_id'] ?? '' }}" placeholder="ETH-20260505-00001">
            </div>
            <div class="col-12 col-md-3 col-xl-2">
                <label for="date_range" class="form-label">Rentang Tanggal</label>
                <select id="date_range" name="date_range" class="form-select">
                    <option value="all" {{ ($filters['date_range'] ?? '') === 'all' || ($filters['date_range'] ?? '') === '' ? 'selected' : '' }}>Semua</option>
                    <option value="week" {{ ($filters['date_range'] ?? '') === 'week' ? 'selected' : '' }}>Mingguan</option>
                    <option value="month" {{ ($filters['date_range'] ?? '') === 'month' ? 'selected' : '' }}>Bulanan</option>
                    <option value="last_month" {{ ($filters['date_range'] ?? '') === 'last_month' ? 'selected' : '' }}>Bulan Kemarin</option>
                    <option value="last_3_months" {{ ($filters['date_range'] ?? '') === 'last_3_months' ? 'selected' : '' }}>3 Bulan Lalu</option>
                    <option value="year" {{ ($filters['date_range'] ?? '') === 'year' ? 'selected' : '' }}>Tahun Ini</option>
                    <option value="last_year" {{ ($filters['date_range'] ?? '') === 'last_year' ? 'selected' : '' }}>Tahun Kemarin</option>
                    <option value="custom" {{ ($filters['date_range'] ?? '') === 'custom' ? 'selected' : '' }}>Custom Range</option>
                </select>
            </div>
            <div class="col-6 col-md-3 col-xl-2" id="date_start_wrap">
                <label for="date_start" class="form-label">Tanggal Mulai</label>
                <input type="date" id="date_start" name="date_start" class="form-control" value="{{ $filters['date_start'] ?? '' }}">
            </div>
            <div class="col-6 col-md-3 col-xl-2" id="date_end_wrap">
                <label for="date_end" class="form-label">Tanggal Akhir</label>
                <input type="date" id="date_end" name="date_end" class="form-control" value="{{ $filters['date_end'] ?? '' }}">
            </div>
            <div class="col-12 col-md-3 col-xl-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">Terapkan Filter</button>
                <a href="{{ panel_route('admin.bookings.list') }}" class="btn btn-light w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

@include('pages.admin.partials.data-table', [
    'tableTitle' => 'List Booking',
    'tableBadge' => 'Semua Status',
    'columns' => $columns,
    'rows' => $rows,
    'pagination' => $pagination,
    'emptyMessage' => 'Belum ada booking yang cocok dengan filter.',
])
@endsection

@push('styles')
<style>
.booking-filter-strip {
    display: flex;
    gap: 0.75rem;
    overflow-x: auto;
    padding-bottom: 0.35rem;
    scrollbar-width: thin;
}

.booking-filter-pill {
    min-width: 220px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    padding: 0.95rem 1rem;
    border-radius: 1rem;
    border: 1px solid currentColor;
    text-decoration: none;
    white-space: nowrap;
    flex: 0 0 auto;
    font-weight: 600;
}

.booking-filter-pill:hover {
    text-decoration: none;
}

.booking-filter-pill__label {
    overflow: hidden;
    text-overflow: ellipsis;
}

.booking-filter-pill__count {
    flex-shrink: 0;
    font-size: 0.85rem;
}

@media (max-width: 767.98px) {
    .booking-filter-pill {
        min-width: 260px;
    }
}

.booking-table-pagination-wrap {
    overflow-x: auto;
}

.booking-table-pagination {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    list-style: none;
    padding: 0;
    white-space: nowrap;
}

.booking-table-pagination li {
    flex: 0 0 auto;
}

.booking-table-pagination a,
.booking-table-pagination span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 42px;
    height: 42px;
    padding: 0 0.95rem;
    border-radius: 0.9rem;
    border: 1px solid rgba(255, 255, 255, 0.08);
    background: rgba(255, 255, 255, 0.02);
    color: inherit;
    font-weight: 600;
    text-decoration: none;
}

.booking-table-pagination a:hover {
    background: rgba(var(--primary-rgb), 0.12);
    border-color: rgba(var(--primary-rgb), 0.35);
    color: rgb(var(--primary-rgb));
}

.booking-table-pagination .is-active span {
    background: rgb(var(--primary-rgb));
    border-color: rgb(var(--primary-rgb));
    color: #fff;
}

.booking-table-pagination .is-disabled span {
    opacity: 0.45;
    cursor: not-allowed;
}

@media (max-width: 767.98px) {
    .booking-table-pagination a,
    .booking-table-pagination span {
        height: 38px;
        min-width: 38px;
        padding: 0 0.8rem;
        border-radius: 0.8rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var dateRange = document.getElementById('date_range');
    var startWrap = document.getElementById('date_start_wrap');
    var endWrap = document.getElementById('date_end_wrap');
    if (!dateRange || !startWrap || !endWrap) return;

    function toggleDateInputs() {
        var isCustom = dateRange.value === 'custom';
        startWrap.style.display = isCustom ? '' : 'none';
        endWrap.style.display = isCustom ? '' : 'none';
        document.getElementById('date_start').disabled = !isCustom;
        document.getElementById('date_end').disabled = !isCustom;
    }

    dateRange.addEventListener('change', toggleDateInputs);
    toggleDateInputs();
});
</script>
@endpush
