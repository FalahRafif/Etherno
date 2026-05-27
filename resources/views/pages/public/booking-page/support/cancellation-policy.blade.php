@extends('layouts.guest.guest')

@section('content')
<section class="section-block container booking-section">
  <div class="section-heading booking-heading">
    <p class="eyebrow">Kebijakan Pembatalan</p>
    <h2>Informasi resmi reschedule, cancellation, dan force majeure</h2>
    <p class="section-lead">Silakan baca kebijakan berikut sebelum melakukan perubahan jadwal atau pembatalan.</p>
  </div>

  <div class="booking-grid booking-grid-single">
    <article class="booking-panel">
      <h3>Kebijakan Utama</h3>
      <ul class="estimate-list">
        <li>DP bersifat non-refundable jika customer melakukan pembatalan.</li>
        <li>Reschedule maksimal 2 minggu sebelum acara dan menyesuaikan ketersediaan jadwal.</li>
        <li>Jika fotografer berhalangan, Etherno menyediakan pengganti tanpa biaya tambahan.</li>
        <li>Pada cuaca buruk, sesi dapat dihentikan atau dipindahkan sesuai keputusan lapangan.</li>
        <li>Untuk kondisi ekstrem, refund dapat diproses setelah dikurangi biaya operasional.</li>
      </ul>

      <div class="estimate-box">
        <p class="estimate-title">Koordinasi Lanjutan</p>
        <p class="estimate-note">Semua koordinasi lanjutan tetap diproses melalui WhatsApp agar cepat dan terdokumentasi.</p>
      </div>
    </article>
  </div>

  @include('pages.public.booking-page.sections.support-links')
</section>
@endsection

