@extends('layouts.guest.guest')

@section('content')
<section class="section-block container booking-section">
  <div class="section-heading booking-heading">
    <p class="eyebrow">Detail Booking</p>
    <h2>Detail booking Anda</h2>
    <p class="section-lead">Masukkan kode booking, lalu verifikasi dengan 4 digit terakhir nomor WhatsApp agar data booking tampil aman dan akurat.</p>
  </div>

  <div class="booking-grid booking-grid-single">
    <form class="booking-panel" id="booking_status_lookup_form" action="#" method="get" data-status-lookup-url="{{ route('booking.status.lookup', [], false) }}">
      <h3>Cari Data Booking</h3>
      <div class="booking-form-grid">
        <div class="form-field form-field-full">
          <label class="form-label" for="booking_code">Kode Booking / Case ID / Kode Request</label>
          <input class="form-input" type="text" id="booking_code" name="booking_code" placeholder="Contoh: ETH-20260528-00001 atau ETH-REQ-2026-000001" required>
          <p class="booking-disclaimer mt-2 mb-0">Jika Anda booking lewat form, gunakan <strong>Case ID</strong> atau <strong>Kode Request</strong> dari halaman sukses booking.</p>
        </div>
      </div>
      <p class="booking-disclaimer text-danger mb-0" id="booking_status_lookup_error" hidden></p>
      <p class="booking-actions"><button class="cta booking-submit" id="booking_status_lookup_button" type="submit">Cek Status Booking</button></p>
    </form>

    <article class="booking-panel booking-status-panel" id="booking_status_result" hidden>
      <h3>Informasi Booking Anda</h3>
      <div class="booking-support-state neutral" id="booking_status_state">
        <strong id="booking_status_state_label">Status booking</strong>
        <span id="booking_status_state_subtitle">Data status booking terbaru akan tampil di sini.</span>
      </div>

      <div class="booking-status-grid mt-3">
        <div class="booking-status-item">
          <p class="booking-status-key">Case ID</p>
          <p class="booking-status-value" id="status_case_id">-</p>
        </div>
        <div class="booking-status-item">
          <p class="booking-status-key">Kode Request</p>
          <p class="booking-status-value" id="status_request_code">-</p>
        </div>
        <div class="booking-status-item">
          <p class="booking-status-key">Nama Customer</p>
          <p class="booking-status-value" id="status_customer_name">-</p>
        </div>
        <div class="booking-status-item">
          <p class="booking-status-key">No WhatsApp</p>
          <p class="booking-status-value" id="status_customer_phone">-</p>
        </div>
        <div class="booking-status-item">
          <p class="booking-status-key">Tanggal Acara</p>
          <p class="booking-status-value" id="status_event_date">-</p>
        </div>
        <div class="booking-status-item">
          <p class="booking-status-key">Sesi Acara</p>
          <p class="booking-status-value" id="status_event_session">-</p>
        </div>
        <div class="booking-status-item">
          <p class="booking-status-key">Paket</p>
          <p class="booking-status-value" id="status_package_name">-</p>
        </div>
        <div class="booking-status-item">
          <p class="booking-status-key">Tipe Paket</p>
          <p class="booking-status-value" id="status_package_type">-</p>
        </div>
        <div class="booking-status-item">
          <p class="booking-status-key">Case ID Paket</p>
          <p class="booking-status-value" id="status_package_case_id">-</p>
        </div>
        <div class="booking-status-item">
          <p class="booking-status-key">Harga Paket</p>
          <p class="booking-status-value" id="status_package_price">-</p>
        </div>
        <div class="booking-status-item booking-status-item-full">
          <p class="booking-status-key">Alamat Paket</p>
          <p class="booking-status-value" id="status_package_address">-</p>
        </div>
        <div class="booking-status-item booking-status-item-full">
          <p class="booking-status-key">Lokasi Acara</p>
          <p class="booking-status-value" id="status_location">-</p>
        </div>
        <div class="booking-status-item booking-status-item-full">
          <p class="booking-status-key">Detail Acara</p>
          <p class="booking-status-value booking-status-pre" id="status_event_detail">-</p>
        </div>
        <div class="booking-status-item booking-status-item-full">
          <p class="booking-status-key">Pin Lokasi</p>
          <p class="booking-status-value"><a href="#" target="_blank" rel="noopener" id="status_google_maps_pin_link">Lihat pin lokasi</a></p>
        </div>
      </div>

      <div class="booking-estimate-item mt-3 pt-3">
        <p class="booking-estimate-label">Ringkasan Pembayaran</p>
        <p class="booking-status-value mb-1" id="status_billing_status">Belum ada data pembayaran.</p>
        <p class="booking-disclaimer mt-0 mb-0" id="status_billing_total">Total tagihan: -</p>
        <p class="booking-disclaimer mt-1 mb-0" id="status_billing_paid">Total dibayar: -</p>
        <p class="booking-disclaimer mt-1 mb-0" id="status_billing_remaining">Sisa pembayaran: -</p>
      </div>

      <div class="booking-estimate-item mt-3 pt-3">
        <p class="booking-estimate-label">Riwayat Status Booking</p>
        <ul class="booking-status-history" id="booking_status_history"></ul>
      </div>

      <div class="booking-support-actions mt-3">
        <a class="cta cta-outline" href="#" id="status_download_proof" hidden>Unduh Bukti Pengajuan (PDF)</a>
      </div>
    </article>
  </div>

  <div class="booking-confirm-modal" id="booking_status_verify_modal" hidden>
    <div class="booking-confirm-backdrop" data-booking-status-verify-close></div>
    <div class="booking-confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="booking_status_verify_title">
      <div class="booking-confirm-header">
        <h4 id="booking_status_verify_title">Verifikasi Data Booking</h4>
        <button class="booking-confirm-close" type="button" aria-label="Tutup verifikasi" data-booking-status-verify-close>&times;</button>
      </div>

      <p class="booking-caption mb-2">Masukkan 4 digit terakhir nomor WhatsApp yang dipakai saat booking.</p>
      <div class="form-field mb-0">
        <label class="form-label" for="booking_status_phone_last4">4 Digit Terakhir No WhatsApp</label>
        <input class="form-input" type="text" id="booking_status_phone_last4" inputmode="numeric" maxlength="4" pattern="\d{4}" placeholder="Contoh: 1234" required>
      </div>
      <p class="booking-disclaimer text-danger mt-2 mb-0" id="booking_status_verify_error" hidden></p>

      <div class="booking-confirm-actions">
        <button class="cta cta-outline" type="button" data-booking-status-verify-close>Batal</button>
        <button class="cta" type="button" id="booking_status_verify_submit">Verifikasi & Tampilkan</button>
      </div>
    </div>
  </div>

  @include('pages.public.booking-page.sections.support-links')
</section>
@endsection
