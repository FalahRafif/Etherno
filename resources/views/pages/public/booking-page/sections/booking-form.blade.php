<div class="booking-grid">
  <aside class="booking-panel booking-availability" aria-labelledby="availability_title">
    <h3 id="availability_title">Cek Ketersediaan Tanggal</h3>
    <p class="booking-caption">Maksimal 2 booking per hari, terbagi sesi pagi-siang dan sore-malam. Slot baru terblokir setelah DP diverifikasi.</p>

    <label class="form-label" for="booking_date_check">Tanggal Acara</label>
    <input class="form-input" type="date" id="booking_date_check" name="booking_date_check">

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

    <p class="booking-note">First come first serve mengikuti urutan DP terverifikasi.</p>
  </aside>

  <form class="booking-panel booking-form" id="booking_form_preview" action="{{ route('booking.success') }}" method="get">
    <h3>Request Booking</h3>

    <div class="booking-form-grid">
      <div class="form-field">
        <label class="form-label" for="booking_name">Nama Lengkap</label>
        <input class="form-input" type="text" id="booking_name" name="booking_name" placeholder="Contoh: Aulia Pratama">
      </div>

      <div class="form-field">
        <label class="form-label" for="booking_whatsapp">No WhatsApp</label>
        <input class="form-input" type="tel" id="booking_whatsapp" name="booking_whatsapp" placeholder="08xxxxxxxxxx">
      </div>

      <div class="form-field">
        <label class="form-label" for="booking_date">Tanggal Acara</label>
        <input class="form-input" type="date" id="booking_date" name="booking_date">
      </div>

      <div class="form-field">
        <label class="form-label" for="booking_session">Sesi Acara</label>
        <select class="form-input form-select" id="booking_session" name="booking_session">
          <option value="">Pilih sesi</option>
          <option value="morning">Pagi-Siang</option>
          <option value="evening">Sore-Malam</option>
        </select>
      </div>

      <div class="form-field">
        <label class="form-label" for="booking_type">Jenis Acara</label>
        <select class="form-input form-select" id="booking_type" name="booking_type">
          <option value="">Pilih jenis acara</option>
          <option value="wedding">Wedding (DP 15%)</option>
          <option value="non_wedding">Non-wedding (DP 10%)</option>
        </select>
      </div>

      <div class="form-field">
        <label class="form-label" for="booking_package">Paket</label>
        <select class="form-input form-select" id="booking_package" name="booking_package">
          <option value="">Pilih paket</option>
          <option value="intim">Intim</option>
          <option value="andalan">Andalan</option>
          <option value="mewah">Mewah</option>
        </select>
      </div>

      <div class="form-field">
        <label class="form-label" for="booking_city">Kota Acara</label>
        <input class="form-input" type="text" id="booking_city" name="booking_city" placeholder="Contoh: Bandung">
      </div>

      <div class="form-field">
        <label class="form-label" for="booking_maps_pin">Link Pin Google Maps (Wajib)</label>
        <input class="form-input" type="url" id="booking_maps_pin" name="booking_maps_pin" placeholder="https://maps.google.com/...">
      </div>

      <div class="form-field form-field-full">
        <label class="form-label" for="booking_detail">Detail Acara</label>
        <textarea class="form-input form-textarea" id="booking_detail" name="booking_detail" rows="4" placeholder="Tuliskan rundown singkat, kebutuhan khusus, dan catatan penting lainnya."></textarea>
      </div>
    </div>

    <div class="estimate-box">
      <p class="estimate-title">Estimasi Biaya Tambahan Lokasi</p>
      <ul class="estimate-list">
        <li>Jabodetabek/Bandung: tambahan ringan</li>
        <li>Luar kota (Jawa): tambahan sedang</li>
        <li>Luar pulau: custom (transport + akomodasi)</li>
      </ul>
      <p class="estimate-note">Biaya tambahan (transport, akomodasi, dll) akan disesuaikan berdasarkan lokasi dan dikonfirmasi setelah pengecekan.</p>
    </div>

    <div class="booking-actions">
      <button class="cta booking-submit" type="submit">Kirim Request Booking</button>
      <p class="booking-disclaimer">Preview frontend: simulasi submit akan diarahkan ke halaman konfirmasi request.</p>
      <p class="booking-disclaimer"><a href="{{ route('booking.status') }}">Sudah pernah booking? Cek status di sini.</a></p>
    </div>
  </form>
</div>
