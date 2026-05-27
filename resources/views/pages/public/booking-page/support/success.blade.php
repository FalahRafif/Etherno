@extends('layouts.guest.guest')

@section('content')
<section class="section-block container booking-section">
  <div class="booking-grid booking-grid-single">
    <article class="booking-panel booking-support-card">
      <p class="eyebrow">Request Terkirim</p>
      <h2 class="final-cta-title booking-support-title">Request booking Anda sudah masuk</h2>
      <p class="booking-caption">Admin Etherno akan meninjau data terlebih dahulu sebelum tahap pembayaran DP. Booking belum dianggap fix sebelum DP diverifikasi.</p>

      <div class="booking-support-state success">
        <strong>OK</strong>
        <span>Status awal: <strong>under_review</strong></span>
      </div>

      <div class="booking-form-grid booking-support-actions">
        <a class="cta" href="{{ route('booking.status') }}">Cek Status Booking</a>
        <a class="cta cta-outline" href="{{ route('home') }}">Kembali ke Landing Page</a>
      </div>
    </article>
  </div>

  @include('pages.public.booking-page.sections.support-links')
</section>
@endsection

