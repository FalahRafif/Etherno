@extends('layouts.admin.admin')

@section('title', $title ?? 'Etherno Admin')

@section('content')
<div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-3 my-3">
    <div>
        <h1 class="page-title mb-1">{{ $pageTitle }}</h1>
        <p class="text-muted mb-0">{{ $pageSummary }}</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.bookings.requests') }}" class="btn btn-outline-primary btn-sm">Booking Requests</a>
        <a href="{{ route('admin.payments.dp') }}" class="btn btn-outline-primary btn-sm">DP Verification</a>
        <a href="{{ route('admin.calendar') }}" class="btn btn-primary btn-sm">Calendar & Slots</a>
    </div>
</div>

@if(!empty($bookingCode))
<div class="alert alert-info" role="alert">
    Preview detail untuk booking code: <strong>{{ $bookingCode }}</strong>
</div>
@endif

<div class="row g-3 mb-3">
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card custom-card">
            <div class="card-body">
                <p class="text-muted mb-1">Request Baru</p>
                <h4 class="mb-0">14</h4>
                <small class="text-muted">Status `submitted` / `under_review`</small>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card custom-card">
            <div class="card-body">
                <p class="text-muted mb-1">DP Pending Verifikasi</p>
                <h4 class="mb-0">7</h4>
                <small class="text-muted">Menunggu review admin</small>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card custom-card">
            <div class="card-body">
                <p class="text-muted mb-1">Booking Aktif</p>
                <h4 class="mb-0">22</h4>
                <small class="text-muted">DP verified, slot terkunci</small>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card custom-card">
            <div class="card-body">
                <p class="text-muted mb-1">Pelunasan H-1</p>
                <h4 class="mb-0">5</h4>
                <small class="text-muted">Perlu follow-up hari ini</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-xl-8">
        <div class="card custom-card mb-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Antrian Operasional</h5>
                <span class="badge bg-primary-transparent text-primary">Preview</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table text-nowrap mb-0">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Customer</th>
                                <th>Tanggal</th>
                                <th>Status Booking</th>
                                <th>Status Payment</th>
                                <th>Aksi Cepat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>ETH-2026-001</td>
                                <td>Ana & Bimo</td>
                                <td>2026-06-21</td>
                                <td><span class="badge bg-warning-transparent text-warning">under_review</span></td>
                                <td><span class="badge bg-light text-dark">unpaid</span></td>
                                <td><a href="{{ route('admin.bookings.detail', ['booking' => 'eth-2026-001']) }}" class="btn btn-sm btn-light">Lihat</a></td>
                            </tr>
                            <tr>
                                <td>ETH-2026-014</td>
                                <td>Rani Putri</td>
                                <td>2026-06-25</td>
                                <td><span class="badge bg-success-transparent text-success">active</span></td>
                                <td><span class="badge bg-info-transparent text-info">dp_verified</span></td>
                                <td><a href="{{ route('admin.pricing.reviews') }}" class="btn btn-sm btn-light">Final Price</a></td>
                            </tr>
                            <tr>
                                <td>ETH-2026-018</td>
                                <td>Dika & Lala</td>
                                <td>2026-06-19</td>
                                <td><span class="badge bg-secondary-transparent text-secondary">final_payment_pending</span></td>
                                <td><span class="badge bg-danger-transparent text-danger">final_pending_verification</span></td>
                                <td><a href="{{ route('admin.payments.final') }}" class="btn btn-sm btn-light">Verifikasi</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-4">
        <div class="card custom-card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Status Model</h5>
            </div>
            <div class="card-body">
                <p class="mb-2"><span class="badge bg-primary-transparent text-primary me-2">Booking</span>`submitted`, `under_review`, `approved`, `active`, `paid`</p>
                <p class="mb-2"><span class="badge bg-success-transparent text-success me-2">Payment</span>`dp_pending_verification`, `dp_verified`, `final_verified`</p>
                <p class="mb-0"><span class="badge bg-danger-transparent text-danger me-2">Exception</span>`expired`, `cancelled`, `force_majeure`</p>
            </div>
        </div>
        <div class="card custom-card mb-0">
            <div class="card-header">
                <h5 class="card-title mb-0">Checklist Halaman</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="d-flex justify-content-between py-1"><span>Public booking flow</span><span class="text-success">Done</span></li>
                    <li class="d-flex justify-content-between py-1"><span>Admin preview pages</span><span class="text-success">Done</span></li>
                    <li class="d-flex justify-content-between py-1"><span>Backend data binding</span><span class="text-warning">Next</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
