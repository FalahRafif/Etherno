@php
    $isEdit = isset($managedRule) && $managedRule instanceof \App\Models\LocationPricingRule;
    $locationOptions = collect($locationOptions ?? []);
    $provinceOptions = $locationOptions->where('level_code', 'LL_PV')->values();
    $cityOptions = $locationOptions->where('level_code', 'LL_CT')->values();
    $selectedLocationId = (string) old('location_id', $isEdit ? (string) $managedRule->location_id : '');
    $selectedPriceTypeId = (string) old('price_type', $isEdit ? (string) $managedRule->price_type : '');
@endphp

@if ($errors->has('general'))
    <div class="alert alert-danger mb-3" role="alert">{{ $errors->first('general') }}</div>
@endif

<form method="POST" action="{{ $formAction }}" class="lpr-form-card">
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
                <div class="col-12 col-lg-6">
                    <label for="location_id" class="form-label">Lokasi <span class="text-danger">*</span></label>
                    <select id="location_id" name="location_id" class="form-select select2 @error('location_id') is-invalid @enderror" required>
                        <option value="">Pilih lokasi</option>

                        @if($provinceOptions->isNotEmpty())
                            <optgroup label="Provinsi">
                                @foreach($provinceOptions as $option)
                                    <option value="{{ $option['id'] }}" @selected($selectedLocationId === (string) $option['id'])>
                                        {{ $option['display_name'] }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif

                        @if($cityOptions->isNotEmpty())
                            <optgroup label="Kota / Kabupaten">
                                @foreach($cityOptions as $option)
                                    <option value="{{ $option['id'] }}" @selected($selectedLocationId === (string) $option['id'])>
                                        {{ $option['display_name'] }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif
                    </select>
                    @error('location_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted d-block mt-1">Pilih provinsi untuk aturan umum, atau kota/kabupaten untuk aturan yang lebih spesifik.</small>
                </div>

                <div class="col-12 col-lg-6">
                    <label for="price_type" class="form-label">Tipe Harga <span class="text-danger">*</span></label>
                    <select id="price_type" name="price_type" class="form-select select2 @error('price_type') is-invalid @enderror" required>
                        <option value="">Pilih tipe harga</option>
                        @foreach(($priceTypeOptions ?? collect()) as $priceType)
                            <option value="{{ $priceType->id }}" @selected($selectedPriceTypeId === (string) $priceType->id)>
                                {{ $priceType->description }} ({{ $priceType->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('price_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted d-block mt-1">Contoh: Tambahan Ringan, Tambahan Sedang, atau Tambahan Custom.</small>
                </div>
            </div>
        </div>

        <div class="card-footer d-flex gap-2">
            <a href="{{ route('admin.location.rules') }}" class="btn btn-light">Kembali</a>
            <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
        </div>
    </div>
</form>
