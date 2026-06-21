@extends('layouts.admin.admin')

@section('title', $title)

@section('content')
@php
    $actions = [
        ['label' => 'Tambah Konfigurasi', 'url' => route('admin.operational-config.create'), 'class' => 'btn btn-primary btn-sm'],
    ];
@endphp

@include('pages.admin.partials.page-header', [
    'heading' => 'Aturan Aplikasi',
    'summary' => 'Kelola konfigurasi operasional aplikasi seperti nomor WhatsApp admin dan pengaturan umum lainnya.',
    'actions' => $actions,
])

@if (session('status'))
    <div class="alert alert-success mb-3" role="alert">{{ session('status') }}</div>
@endif

@if ($errors->has('general'))
    <div class="alert alert-danger mb-3" role="alert">{{ $errors->first('general') }}</div>
@endif

@include('pages.admin.partials.alerts', ['alerts' => []])

<div class="card custom-card mb-0">
    <div class="card-header">
        <h5 class="card-title mb-0">Daftar Konfigurasi Aplikasi</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover text-nowrap align-middle mb-0">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Deskripsi</th>
                        <th>Nilai</th>
                        <th>Diperbarui</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(($rules ?? collect()) as $rule)
                        <tr>
                            <td><code>{{ $rule->code }}</code></td>
                            <td>{{ $rule->description }}</td>
                            <td><span class="badge bg-primary-transparent text-primary">{{ $rule->value }}</span></td>
                            <td>{{ $rule->updated_at?->format('Y-m-d H:i') ?? '-' }}</td>
                            <td class="text-end">
                                <div class="btn-list justify-content-end">
                                    <a href="{{ route('admin.operational-config.edit', $rule) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form method="POST" action="{{ route('api.admin.operational-config.destroy', $rule) }}" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus konfigurasi ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada data konfigurasi aplikasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
