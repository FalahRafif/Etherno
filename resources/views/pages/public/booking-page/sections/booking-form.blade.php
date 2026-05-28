@php
  $packageTypeOptions = $packageTypeOptions ?? collect();
  $packageOptions = $packageOptions ?? collect();
  $eventSessionOptions = $eventSessionOptions ?? collect();
  $provinceOptions = $provinceOptions ?? collect();

  $selectedPackageTypeId = (string) old('booking_package_type', '');
  $selectedProvinceId = (string) old('booking_location_province', '');
  $selectedCityId = (string) old('booking_location_city', '');
  $selectedDistrictId = (string) old('booking_location_district', '');
  $selectedVillageId = (string) old('booking_location_village', '');
@endphp

<div class="booking-grid">
  <aside class="booking-panel booking-availability" aria-labelledby="availability_title">
    <h3 id="availability_title">Cek Ketersediaan Tanggal</h3>
    <p class="booking-caption">Maksimal 2 booking per hari, terbagi sesi pagi-siang dan sore-malam. Slot baru terblokir setelah DP diverifikasi.</p>

    <label class="form-label" for="booking_date_check">Tanggal Acara</label>
    <input
      class="form-input"
      type="date"
      id="booking_date_check"
      name="booking_date"
      form="booking_form_preview"
      value="{{ old('booking_date') }}"
      data-availability-url="{{ route('booking.availability', [], false) }}"
      required>
    @error('booking_date')
      <p class="booking-disclaimer text-danger mb-0">{{ $message }}</p>
    @enderror

    <p class="availability-summary" id="availability_summary">Pilih tanggal untuk melihat status slot.</p>

    <div class="slot-list">
      <article class="slot-card" data-slot-card="morning">
        <p class="slot-name">Pagi-Siang</p>
        <p class="slot-status" data-slot-status="morning">Belum dipilih</p>
      </article>
      <article class="slot-card" data-slot-card="evening">
        <p class="slot-name">Sore-Malam</p>
        <p class="slot-status" data-slot-status="evening">Belum dipilih</p>
      </article>
    </div>

    <div class="form-field mt-3">
      <label class="form-label" for="booking_session">Sesi Acara</label>
      <select class="form-input form-select booking-select2" id="booking_session" name="booking_session" form="booking_form_preview" data-placeholder="Pilih sesi acara" required>
        <option value="">Pilih sesi acara</option>
        @foreach ($eventSessionOptions as $sessionOption)
          <option value="{{ $sessionOption->id }}" data-session-code="{{ strtoupper((string) $sessionOption->code) }}" @selected((string) old('booking_session') === (string) $sessionOption->id)>
            {{ $sessionOption->description }}
          </option>
        @endforeach
      </select>
      @error('booking_session')
        <p class="booking-disclaimer text-danger mb-0">{{ $message }}</p>
      @enderror
    </div>

    <p class="booking-note">First come first serve mengikuti urutan DP terverifikasi.</p>

    <section class="booking-estimate-panel" id="booking_estimate_panel" data-estimate-url="{{ route('booking.estimate', [], false) }}">
      <h4 class="booking-estimate-title">Perkiraan Biaya Awal Booking</h4>
      <p class="booking-disclaimer mt-1 mb-2">Bagian ini membantu Anda melihat gambaran biaya awal berdasarkan paket yang dipilih, kategori lokasi acara, dan skema pembayaran.</p>

      <div class="booking-estimate-item">
        <p class="booking-estimate-label">Ringkasan Paket</p>
        <p class="booking-estimate-value" id="estimate_package_name">Pilih tipe paket dan paket untuk melihat perkiraan biaya.</p>
        <p class="booking-disclaimer mt-1 mb-0" id="estimate_package_type">-</p>
        <p class="booking-disclaimer mt-1 mb-0" id="estimate_package_price">Harga paket: -</p>
        <p class="booking-disclaimer mt-1 mb-0" id="estimate_package_address">Alamat paket: -</p>
        <ul class="booking-estimate-benefits" id="estimate_package_benefits"></ul>
      </div>

      <div class="booking-estimate-item">
        <p class="booking-estimate-label">Biaya Tambahan Lokasi</p>
        <p class="booking-estimate-value" id="estimate_location_rule">Lengkapi lokasi acara untuk melihat kategori tambahan biaya.</p>
        <p class="booking-disclaimer mt-1 mb-0">Biaya tambahan akan dikenakan berdasarkan kategori lokasi acara.</p>
      </div>

      <div class="booking-estimate-item">
        <p class="booking-estimate-label">Rencana Pembayaran</p>
        <p class="booking-estimate-value mb-1" id="estimate_dp_percentage">DP: -</p>
        <p class="booking-disclaimer mt-0 mb-0" id="estimate_dp_amount">Nominal DP: -</p>
        <p class="booking-disclaimer mt-1 mb-0" id="estimate_dp_note">Batas waktu DP: -</p>
        <p class="booking-disclaimer mt-1 mb-0" id="estimate_final_note">Batas pelunasan: -</p>
        <p class="booking-disclaimer mt-1 mb-0" id="estimate_final_due_date">Tanggal batas pelunasan: -</p>
      </div>

      <p class="booking-estimate-warning mb-0">Catatan penting: ini masih estimasi awal. Harga akhir bisa bertambah setelah tim meninjau detail lokasi, akses venue, kebutuhan teknis acara, transportasi, dan akomodasi.</p>
    </section>
  </aside>

  <form
    class="booking-panel booking-form"
    id="booking_form_preview"
    action="{{ route('booking.store', [], false) }}"
    method="post"
    data-location-options-url="{{ route('booking.location.options', [], false) }}"
    data-estimate-url="{{ route('booking.estimate', [], false) }}">
    @csrf

    <h3>Request Booking</h3>

    @if ($errors->has('general'))
      <div class="alert alert-danger mb-3" role="alert">{{ $errors->first('general') }}</div>
    @endif

    <div class="booking-form-grid">
      <div class="form-field">
        <label class="form-label" for="booking_name">Nama Lengkap</label>
        <input class="form-input" type="text" id="booking_name" name="booking_name" value="{{ old('booking_name') }}" placeholder="Contoh: Aulia Pratama" required>
        @error('booking_name')
          <p class="booking-disclaimer text-danger mb-0">{{ $message }}</p>
        @enderror
      </div>

      <div class="form-field">
        <label class="form-label" for="booking_whatsapp">No WhatsApp</label>
        <input class="form-input" type="tel" id="booking_whatsapp" name="booking_whatsapp" value="{{ old('booking_whatsapp') }}" placeholder="08xxxxxxxxxx" required>
        @error('booking_whatsapp')
          <p class="booking-disclaimer text-danger mb-0">{{ $message }}</p>
        @enderror
      </div>

      <div class="form-field">
        <label class="form-label" for="booking_package_type">Tipe Paket</label>
        <select class="form-input form-select booking-select2" id="booking_package_type" name="booking_package_type" data-placeholder="Pilih tipe paket" required>
          <option value="">Pilih tipe paket</option>
          @foreach ($packageTypeOptions as $packageTypeOption)
            <option value="{{ $packageTypeOption->id }}" @selected($selectedPackageTypeId === (string) $packageTypeOption->id)>
              {{ $packageTypeOption->description }}
            </option>
          @endforeach
        </select>
        @error('booking_package_type')
          <p class="booking-disclaimer text-danger mb-0">{{ $message }}</p>
        @enderror
      </div>

      <div class="form-field">
        <label class="form-label" for="booking_package">Paket</label>
        <select class="form-input form-select booking-select2" id="booking_package" name="booking_package" data-placeholder="Pilih paket" required>
          <option value="">Pilih paket</option>
          @foreach ($packageOptions as $packageOption)
            @php
              $packageTypeLabel = trim((string) ($packageOption->packageType?->description ?? 'PACKAGE'));
            @endphp
            <option
              value="{{ $packageOption->id }}"
              data-package-type="{{ (string) $packageOption->package_type }}"
              data-package-address="{{ trim((string) ($packageOption->address ?? '')) }}"
              @selected((string) old('booking_package') === (string) $packageOption->id)>
              {{ $packageOption->name }} - Rp {{ number_format((float) $packageOption->price, 0, ',', '.') }} ({{ $packageTypeLabel }})
            </option>
          @endforeach
        </select>
        <p class="booking-disclaimer mt-2 mb-0" id="booking_package_address_preview" data-default-text="Pilih paket untuk melihat referensi alamat paket.">Pilih paket untuk melihat referensi alamat paket.</p>
        @error('booking_package')
          <p class="booking-disclaimer text-danger mb-0">{{ $message }}</p>
        @enderror
      </div>
      <div class="form-field form-field-full">
        <label class="form-label" for="booking_detail">Detail Acara</label>
        <textarea class="form-input form-textarea" id="booking_detail" name="booking_detail" rows="4" placeholder="Tuliskan rundown singkat, kebutuhan khusus, dan catatan penting lainnya.">{{ old('booking_detail') }}</textarea>
        @error('booking_detail')
          <p class="booking-disclaimer text-danger mb-0">{{ $message }}</p>
        @enderror
      </div>

      <div class="form-field form-field-full booking-location-fieldset">
        <label class="form-label" for="booking_location_province">Lokasi Acara</label>
        <p class="booking-disclaimer mb-2">Pilih lokasi berurutan mulai dari provinsi sampai kelurahan agar tim bisa menghitung biaya tambahan lokasi secara akurat.</p>

        <input type="hidden" id="booking_location" name="booking_location" value="{{ old('booking_location', $selectedVillageId) }}">

        <div class="booking-location-grid">
          <div class="booking-location-item">
            <label class="form-label" for="booking_location_province">Provinsi</label>
            <select class="form-input form-select booking-select2" id="booking_location_province" name="booking_location_province" data-placeholder="Pilih provinsi" required>
              <option value="">Pilih provinsi</option>
              @foreach ($provinceOptions as $provinceOption)
                <option value="{{ $provinceOption->id }}" @selected($selectedProvinceId === (string) $provinceOption->id)>
                  {{ $provinceOption->name }}
                </option>
              @endforeach
            </select>
            @error('booking_location_province')
              <p class="booking-disclaimer text-danger mb-0">{{ $message }}</p>
            @enderror
          </div>

          <div class="booking-location-item">
            <label class="form-label" for="booking_location_city">Kota/Kabupaten</label>
            <select
              class="form-input form-select booking-select2"
              id="booking_location_city"
              name="booking_location_city"
              data-placeholder="Pilih kota/kabupaten"
              data-selected="{{ $selectedCityId }}"
              disabled
              required>
              <option value="">Pilih kota/kabupaten</option>
            </select>
            @error('booking_location_city')
              <p class="booking-disclaimer text-danger mb-0">{{ $message }}</p>
            @enderror
          </div>

          <div class="booking-location-item">
            <label class="form-label" for="booking_location_district">Kecamatan</label>
            <select
              class="form-input form-select booking-select2"
              id="booking_location_district"
              name="booking_location_district"
              data-placeholder="Pilih kecamatan"
              data-selected="{{ $selectedDistrictId }}"
              disabled
              required>
              <option value="">Pilih kecamatan</option>
            </select>
            @error('booking_location_district')
              <p class="booking-disclaimer text-danger mb-0">{{ $message }}</p>
            @enderror
          </div>

          <div class="booking-location-item">
            <label class="form-label" for="booking_location_village">Kelurahan</label>
            <select
              class="form-input form-select booking-select2"
              id="booking_location_village"
              name="booking_location_village"
              data-placeholder="Pilih kelurahan"
              data-selected="{{ $selectedVillageId }}"
              disabled
              required>
              <option value="">Pilih kelurahan</option>
            </select>
            @error('booking_location_village')
              <p class="booking-disclaimer text-danger mb-0">{{ $message }}</p>
            @enderror
          </div>
        </div>

        @error('booking_location')
          <p class="booking-disclaimer text-danger mb-0">{{ $message }}</p>
        @enderror

        <div class="booking-pin-item booking-pin-item-full">
            <label class="form-label" for="booking_pin_address">Detail Alamat & Patokan</label>
            <textarea class="form-input form-textarea" id="booking_pin_address" name="booking_pin_address" rows="3" placeholder="Contoh: Gedung Serbaguna X, dekat gerbang utara, lantai 2." required>{{ old('booking_pin_address') }}</textarea>
            @error('booking_pin_address')
              <p class="booking-disclaimer text-danger mb-0">{{ $message }}</p>
            @enderror
          </div>
      </div>
      
      <div class="form-field form-field-full booking-pin-fieldset">
        <label class="form-label" for="booking_pin_address">Pin Lokasi Acara</label>
        <p class="booking-disclaimer mb-2">Masukkan titik koordinat lokasi seperti aplikasi transportasi online. Anda bisa copy dari Google Maps dengan menekan lama titik lokasi.</p>

        <div class="booking-map-picker-wrap">
          <div id="booking_map_picker" class="booking-map-picker" aria-label="Peta pemilihan pin lokasi acara"></div>
          <p class="booking-disclaimer mt-2">Klik pada peta untuk meletakkan pin, lalu sesuaikan titik dengan drag marker agar lebih presisi.</p>
        </div>

        <div class="booking-pin-grid">
          <input type="hidden" id="booking_pin_lat" name="booking_pin_lat" value="{{ old('booking_pin_lat') }}">
          <input type="hidden" id="booking_pin_lng" name="booking_pin_lng" value="{{ old('booking_pin_lng') }}">
          <p class="booking-disclaimer mt-0 mb-0 booking-pin-coordinate-hint" id="booking_pin_coordinate_hint">Koordinat belum dipilih. Silakan klik pin pada peta.</p>
          @if($errors->has('booking_pin_lat') || $errors->has('booking_pin_lng'))
            <p class="booking-disclaimer text-danger mb-0">Silakan pilih titik pin di peta terlebih dahulu.</p>
          @endif

        </div>
      </div>
    </div>

    <div class="booking-actions">
      <button class="cta booking-submit" type="submit">Kirim Request Booking</button>
      <p class="booking-disclaimer">Data request akan masuk ke tim admin untuk proses review sebelum tahap pembayaran DP.</p>
      <p class="booking-disclaimer"><a href="{{ route('booking.status') }}">Sudah pernah booking? Cek status di sini.</a></p>
    </div>
  </form>
</div>
