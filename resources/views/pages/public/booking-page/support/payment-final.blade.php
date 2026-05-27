@extends('layouts.guest.guest')

@section('content')
<section class="section-block container booking-section">
  <div class="section-heading booking-heading">
    <p class="eyebrow">Konfirmasi Pelunasan</p>
    <h2>Konfirmasi final payment sebelum H-1 acara</h2>
    <p class="section-lead">Pelunasan tetap mengikuti jalur utama via WhatsApp, dan form ini menjadi dukungan pelacakan di sistem.</p>
  </div>

  <div class="booking-grid booking-grid-single">
    <form class="booking-panel" action="#" method="post">
      <h3>Form Konfirmasi Pelunasan</h3>
      <div class="booking-form-grid">
        <div class="form-field">
          <label class="form-label" for="final_booking_code">Kode Booking</label>
          <input class="form-input" type="text" id="final_booking_code" name="final_booking_code" placeholder="Contoh: ETH-2026-001">
        </div>
        <div class="form-field">
          <label class="form-label" for="final_whatsapp">No WhatsApp</label>
          <input class="form-input" type="tel" id="final_whatsapp" name="final_whatsapp" placeholder="08xxxxxxxxxx">
        </div>
        <div class="form-field form-field-full">
          <label class="form-label" for="final_proof">Bukti Transfer Pelunasan (Opsional)</label>
          <input class="form-input" type="file" id="final_proof" name="final_proof">
        </div>
      </div>

      <div class="estimate-box">
        <p class="estimate-title">Reminder Pelunasan</p>
        <p class="estimate-note">Pelunasan wajib selesai maksimal H-1 acara dan diverifikasi manual oleh admin.</p>
      </div>

      <p class="booking-actions">
        <button class="cta booking-submit" type="button">Kirim Konfirmasi Pelunasan</button>
      </p>
    </form>
  </div>

  @include('pages.public.booking-page.sections.support-links')
</section>
@endsection

