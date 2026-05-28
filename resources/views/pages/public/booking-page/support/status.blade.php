@extends('layouts.guest.guest')

@section('content')
<section class="section-block container booking-section">
  <div class="section-heading booking-heading">
    <p class="eyebrow">Detail Booking</p>
    <h2>Detail booking Anda</h2>
    <p class="section-lead">Gunakan kode booking atau nomor WhatsApp untuk melihat status terbaru dan progres pembayaran.</p>
  </div>

  <div class="booking-grid booking-grid-single">
    <form class="booking-panel" action="#" method="get">
      <h3>Cari Data Booking</h3>
      <div class="booking-form-grid">
        <div class="form-field">
          <label class="form-label" for="booking_code">Kode Booking</label>
          <input class="form-input" type="text" id="booking_code" name="booking_code" placeholder="Contoh: ETH-2026-001">
        </div>
        <div class="form-field">
          <label class="form-label" for="booking_whatsapp_status">No WhatsApp</label>
          <input class="form-input" type="tel" id="booking_whatsapp_status" name="booking_whatsapp_status" placeholder="08xxxxxxxxxx">
        </div>
      </div>
      <p class="booking-disclaimer">Preview frontend: tombol ini belum terhubung ke data backend.</p>
      <p class="booking-actions"><button class="cta booking-submit" type="button">Cek Status</button></p>
    </form>
  </div>

  @include('pages.public.booking-page.sections.support-links')
</section>
@endsection

