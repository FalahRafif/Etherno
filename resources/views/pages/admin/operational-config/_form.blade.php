@php
    $isEdit = isset($managedRule) && $managedRule instanceof \App\Models\Setting;
    $oldCode = old('code', $isEdit ? $managedRule->code : '');
    $oldDescription = old('description', $isEdit ? $managedRule->description : '');
    $oldValue = old('value', $isEdit ? $managedRule->value : '');
@endphp

@if ($errors->has('general'))
    <div class="alert alert-danger mb-3" role="alert">{{ $errors->first('general') }}</div>
@endif

<form method="POST" action="{{ $formAction }}">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="card custom-card mb-0">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ $formTitle }}</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="code" class="form-label">Kode Konfigurasi <span class="text-danger">*</span></label>
                    @if ($isEdit)
                        <input type="text" class="form-control" id="code" value="{{ $oldCode }}" disabled>
                        <input type="hidden" name="code" value="{{ $oldCode }}">
                        <small class="text-muted d-block mt-1">Kode tidak dapat diubah setelah dibuat.</small>
                    @else
                        <input
                            type="text"
                            class="form-control @error('code') is-invalid @enderror"
                            id="code"
                            name="code"
                            value="{{ $oldCode }}"
                            placeholder="Contoh: ADMIN_WHATSAPP"
                            required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-1">Kode unik dalam grup konfigurasi aplikasi.</small>
                    @endif
                </div>
                <div class="col-md-4">
                    <label for="description" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        class="form-control @error('description') is-invalid @enderror"
                        id="description"
                        name="description"
                        value="{{ $oldDescription }}"
                        placeholder="Contoh: Nomor WhatsApp Admin"
                        required>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <hr class="my-4">

            <div class="row g-3">
                <div class="col-md-8">
                    <label for="value" class="form-label">Nilai <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        class="form-control @error('value') is-invalid @enderror"
                        id="value"
                        name="value"
                        value="{{ $oldValue }}"
                        placeholder="Contoh: 6281234567890"
                        required>
                    @error('value')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted d-block mt-1">Masukkan nilai konfigurasi sesuai deskripsi.</small>
                </div>
            </div>
        </div>

        <div class="card-footer d-flex gap-2">
            <a href="{{ route('admin.operational-config') }}" class="btn btn-light">Kembali</a>
            <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
        </div>
    </div>
</form>
