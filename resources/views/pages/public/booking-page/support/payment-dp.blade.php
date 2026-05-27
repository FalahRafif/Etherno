@extends('layouts.guest.guest')

@section('content')
<section class="section-block container booking-section">
  <div class="section-heading booking-heading">
    <p class="eyebrow">Konfirmasi DP</p>
    <h2>Upload bukti DP sebagai support flow</h2>
    <p class="section-lead">Alur utama tetap melalui WhatsApp. Upload di halaman ini bersifat opsional agar admin lebih cepat memvalidasi.</p>
  </div>

  <div class="booking-grid booking-grid-single">
    <form class="booking-panel" action="#" method="post">
      <h3>Form Konfirmasi DP</h3>
      <div class="booking-form-grid">
        <div class="form-field">
          <label class="form-label" for="dp_booking_code">Kode Booking</label>
          <input class="form-input" type="text" id="dp_booking_code" name="dp_booking_code" placeholder="Contoh: ETH-2026-001">
        </div>
        <div class="form-field">
          <label class="form-label" for="dp_whatsapp">No WhatsApp</label>
          <input class="form-input" type="tel" id="dp_whatsapp" name="dp_whatsapp" placeholder="08xxxxxxxxxx">
        </div>
        <div class="form-field form-field-full">
          <label class="form-label" for="dp_proof">Bukti Transfer (Opsional)</label>
          <input class="form-input" type="file" id="dp_proof" name="dp_proof">
        </div>
      </div>

      <div class="estimate-box">
        <p class="estimate-title">Langkah Utama</p>
        <p class="estimate-note">Tetap konfirmasi pembayaran DP via WhatsApp untuk proses verifikasi manual oleh admin.</p>
      </div>

      <p class="booking-actions">
        <button class="cta booking-submit" type="button">Kirim Konfirmasi DP</button>
      </p>
    </form>
  </div>

  @include('pages.public.booking-page.sections.support-links')
</section>
@endsection

