@extends('layouts.admin.admin')

@section('title', $title)

@section('content')
@include('pages.admin.partials.page-header', [
    'heading' => 'Tambah Aturan Aplikasi',
    'summary' => 'Buat konfigurasi aplikasi baru.',
    'actions' => [
        ['label' => 'Kembali ke Daftar', 'url' => route('admin.operational-config'), 'class' => 'btn btn-outline-primary btn-sm'],
    ],
])

@include('pages.admin.operational-config._form', [
    'formTitle' => 'Form Tambah Konfigurasi',
    'formAction' => route('api.admin.operational-config.store'),
    'submitLabel' => 'Simpan Konfigurasi',
])
@endsection
