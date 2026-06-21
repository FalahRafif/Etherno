@extends('layouts.admin.admin')

@section('title', $title)

@section('content')
@include('pages.admin.partials.page-header', [
    'heading' => 'Edit Aturan Aplikasi',
    'summary' => 'Perbarui nilai konfigurasi aplikasi.',
    'actions' => [
        ['label' => 'Kembali ke Daftar', 'url' => route('admin.operational-config'), 'class' => 'btn btn-outline-primary btn-sm'],
    ],
])

@include('pages.admin.operational-config._form', [
    'formTitle' => 'Form Edit Konfigurasi',
    'formAction' => route('api.admin.operational-config.update', $managedRule),
    'submitLabel' => 'Simpan Perubahan',
    'managedRule' => $managedRule,
])
@endsection
