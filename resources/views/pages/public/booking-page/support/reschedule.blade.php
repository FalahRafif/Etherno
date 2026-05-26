@extends('layouts.public.public')

@section('content')
<section class="section-block container booking-section">
  <div class="section-heading booking-heading">
    <p class="eyebrow">Reschedule Request</p>
    <h2>Ajukan perubahan jadwal acara</h2>
    <p class="section-lead">Request reschedule diproses manual dan hanya dapat diajukan maksimal 2 minggu sebelum tanggal acara.</p>
  </div>

  <div class="booking-grid booking-grid-single">
    <form class="booking-panel" action="#" method="post">
      <h3>Form Request Reschedule</h3>
      <div class="booking-form-grid">
        <div class="form-field">
          <label class="form-label" for="reschedule_booking_code">Kode Booking</label>
          <input class="form-input" type="text" id="reschedule_booking_code" name="reschedule_booking_code" placeholder="Contoh: ETH-2026-001">
        </div>
        <div class="form-field">
          <label class="form-label" for="reschedule_whatsapp">No WhatsApp</label>
          <input class="form-input" type="tel" id="reschedule_whatsapp" name="reschedule_whatsapp" placeholder="08xxxxxxxxxx">
        </div>
        <div class="form-field">
          <label class="form-label" for="reschedule_date_old">Tanggal Awal</label>
          <input class="form-input" type="date" id="reschedule_date_old" name="reschedule_date_old">
        </div>
        <div class="form-field">
          <label class="form-label" for="reschedule_date_new">Tanggal Baru (Usulan)</label>
          <input class="form-input" type="date" id="reschedule_date_new" name="reschedule_date_new">
        </div>
        <div class="form-field form-field-full">
          <label class="form-label" for="reschedule_reason">Alasan Reschedule</label>
          <textarea class="form-input form-textarea" id="reschedule_reason" name="reschedule_reason" rows="4" placeholder="Tuliskan alasan perubahan jadwal acara."></textarea>
        </div>
      </div>
      <p class="booking-actions"><button class="cta booking-submit" type="button">Kirim Request</button></p>
    </form>
  </div>

  @include('pages.public.booking-page.sections.support-links')
</section>
@endsection
