# Etherno

Etherno adalah aplikasi pemesanan layanan dokumentasi foto/video untuk kebutuhan wedding dan non-wedding. README ini menjadi panduan produk utama saat membuat task berikutnya agar implementasi tetap mengikuti proses bisnis yang sudah disepakati.

## Product Source Of Truth

Gunakan dokumen ini sebagai acuan sebelum membuat fitur baru, mengubah flow booking, membuat status pembayaran, menulis copywriting publik, atau merancang tampilan admin/customer.

Prinsip utama:

- Booking belum dianggap fix sebelum DP berhasil diverifikasi.
- Slot hanya terblokir setelah DP dibayarkan dan diverifikasi.
- Harga awal hanya menampilkan base price plus estimasi/range biaya tambahan, bukan angka final.
- WhatsApp adalah kanal komunikasi utama untuk pembayaran, koordinasi, reschedule, cancellation, dan follow-up setelah booking.
- Upload bukti pembayaran di sistem bersifat opsional sebagai support flow, bukan pengganti WhatsApp.

## 1. Booking Flow

Customer mengisi form booking dengan data:

- Nama
- Nomor WhatsApp
- Tanggal acara
- Lokasi acara
- Pin Google Maps lokasi acara (wajib)
- Paket yang dipilih
- Detail acara

Flow approval:

- Sistem menggunakan approval sebelum pembayaran.
- Admin melakukan review data booking terlebih dahulu.
- Setelah disetujui admin, customer diminta melakukan pembayaran DP.
- Booking dianggap fix setelah DP dibayarkan dan berhasil diverifikasi.

Implikasi sistem:

- Form booking menghasilkan request/penawaran, bukan booking final.
- Status awal harus mencerminkan bahwa booking masih menunggu review/admin approval.
- Customer tidak boleh dianggap memiliki slot sebelum DP verified.

## 2. Slot And Schedule

Aturan jadwal:

- Maksimal 2 booking per hari.
- Sesi tersedia: pagi-siang dan sore-malam.
- Sistem menerapkan First Come First Serve berdasarkan DP.
- Slot hanya terblokir setelah DP berhasil diverifikasi.
- Jika customer belum membayar DP, slot tetap tersedia untuk customer lain.

Implikasi sistem:

- Slot availability harus dihitung dari booking yang DP-nya verified/active.
- Booking yang masih pending approval, approved but unpaid, atau expired tidak memblokir slot.
- Jika dua booking aktif sudah ada pada tanggal yang sama, tanggal tersebut dianggap penuh.

## 3. Pricing And Location

Tampilan awal di sistem harus menampilkan:

- Harga dasar atau base price dari paket.
- Estimasi biaya tambahan berdasarkan kategori/range lokasi.
- Note transparansi biaya tambahan.

Contoh kategori lokasi:

- Jabodetabek / Bandung: tambahan ringan atau range kecil.
- Luar kota area Jawa: tambahan sedang.
- Luar pulau: custom, termasuk transport dan akomodasi.

Biaya tambahan dapat meliputi:

- Transport
- Akomodasi untuk luar kota atau luar pulau
- Overtime jika durasi lebih dari 8 jam

Aturan display:

- Jangan menampilkan angka final pada tahap awal.
- Tampilkan range atau estimasi agar customer punya gambaran.
- Wajib menampilkan note: "Biaya tambahan (transport, akomodasi, dll) akan disesuaikan berdasarkan lokasi dan dikonfirmasi setelah pengecekan."

Perhitungan lokasi:

- Perhitungan berbasis kota, bukan kilometer.
- Area Jabodetabek masih masuk range tertentu.
- Luar kota dan luar pulau memiliki biaya tambahan.

## 4. Payment System

Metode pembayaran utama:

- Manual transfer.

Flow DP:

- Customer melakukan transfer DP ke rekening Etherno.
- Customer melakukan konfirmasi pembayaran melalui WhatsApp sebagai jalur utama.
- Customer dapat upload bukti pembayaran di sistem sebagai opsi tambahan.
- Admin melakukan verifikasi manual.
- Status booking diupdate di sistem setelah verifikasi.

Implikasi sistem:

- Sistem perlu menyimpan status pembayaran dan status verifikasi manual.
- Upload bukti pembayaran tidak otomatis membuat booking aktif.
- Booking aktif hanya setelah admin melakukan verifikasi DP.

## 5. Payment Scheme

DP menjadi metode utama untuk mengunci booking.

Besaran DP:

- Wedding: 15% dari nilai paket/penawaran.
- Non-wedding: 10% dari nilai paket/penawaran.

Pelunasan:

- Maksimal H-1 acara.
- Pembayaran dilakukan melalui transfer manual.
- Konfirmasi pelunasan dilakukan melalui WhatsApp sebagai jalur utama.

Booking aktif:

- Booking aktif setelah DP berhasil diverifikasi.

## 6. DP Expiration

Batas waktu pembayaran DP:

- Maksimal 3 hari setelah penawaran diberikan.

Jika DP belum dibayar dalam batas waktu:

- Booking dianggap expired.
- Slot kembali tersedia.
- Customer perlu melakukan proses booking/penawaran ulang jika masih ingin melanjutkan.

Implikasi sistem:

- Sistem perlu menyimpan waktu penawaran/approval.
- Sistem perlu mengenali booking yang expired.
- Booking expired tidak boleh memblokir slot.

## 7. After DP And Final Price

Setelah DP dibayarkan dan diverifikasi:

- Admin melakukan pengecekan lokasi.
- Admin menghitung biaya tambahan jika ada.
- Harga final disampaikan melalui WhatsApp sebagai jalur utama.
- Harga final juga dapat ditampilkan di sistem sebagai fitur opsional.

Informasi harga final yang disampaikan:

- Harga final.
- Breakdown biaya tambahan jika ada.

Implikasi sistem:

- Harga final dapat berbeda dari estimasi awal.
- Perubahan harga final harus memiliki ruang untuk catatan admin.
- Breakdown biaya tambahan sebaiknya disimpan agar audit dan komunikasi jelas.

## 8. Final Payment

Flow utama pelunasan:

- Customer melakukan pelunasan melalui transfer manual.
- Customer mengonfirmasi pelunasan melalui WhatsApp.

System support opsional:

- Customer dapat upload bukti pembayaran pelunasan di sistem.
- Upload ini hanya tambahan dan tetap mengikuti flow utama via WhatsApp.

Implikasi sistem:

- Final payment perlu status tersendiri, misalnya unpaid, pending verification, verified.
- Admin tetap menjadi pihak yang memverifikasi pelunasan.
- Pelunasan wajib selesai maksimal H-1 acara.

## 9. After Payment And Coordination

Setelah pembayaran dan booking aktif:

- Semua komunikasi lanjutan dilakukan melalui WhatsApp.
- Sistem boleh menyimpan ringkasan status, tetapi koordinasi operasional tetap manual via WhatsApp.

Contoh komunikasi lanjutan:

- Koordinasi detail acara.
- Konfirmasi timeline.
- Arahan teknis.
- Follow-up kebutuhan lokasi.

## 10. Reschedule And Cancellation

Reschedule:

- Request maksimal 2 minggu sebelum acara.
- Reschedule bergantung pada ketersediaan jadwal.
- Diproses manual melalui WhatsApp.

Cancellation:

- DP hangus.
- DP bersifat non-refundable.

Implikasi sistem:

- Sistem perlu membedakan cancellation dan reschedule.
- Reschedule tidak boleh otomatis diterima tanpa admin review.
- Cancellation harus mempertahankan catatan pembayaran DP.

## 11. Force Majeure Handling

Jika fotografer berhalangan:

- Etherno menyediakan fotografer pengganti.
- Tidak ada biaya tambahan untuk customer.

Jika cuaca buruk:

- Sesi bisa dihentikan atau dipindahkan.
- Cuaca buruk tidak menjadi tanggung jawab penuh fotografer.

Jika kondisi ekstrem:

- Refund dapat dilakukan.
- Refund dikurangi biaya operasional.

Implikasi sistem:

- Admin perlu ruang catatan untuk force majeure.
- Refund force majeure harus diproses manual dan tidak otomatis.
- Status khusus dapat dibuat jika dibutuhkan pada tahap berikutnya.

## Suggested Status Model

Status booking yang disarankan untuk implementasi berikutnya:

- `draft` atau `submitted`: customer sudah mengirim form booking.
- `under_review`: admin sedang melakukan review.
- `approved`: admin menyetujui penawaran, menunggu DP.
- `rejected`: admin menolak request/penawaran.
- `dp_pending`: customer sudah konfirmasi/upload bukti DP, menunggu verifikasi admin.
- `active`: DP verified, booking fix, slot terblokir.
- `expired`: DP tidak dibayar dalam 3 hari setelah penawaran.
- `final_payment_pending`: customer sudah konfirmasi/upload bukti pelunasan.
- `paid`: pelunasan verified.
- `reschedule_requested`: customer meminta reschedule.
- `rescheduled`: tanggal/sesi sudah dipindahkan.
- `cancelled`: booking dibatalkan.
- `force_majeure`: booking terkena kondisi force majeure.

Catatan:

- Nama status bisa disesuaikan dengan pola kode yang sudah ada.
- Pastikan hanya status `active`, `paid`, dan status aktif lain yang memang sudah DP verified yang memblokir slot.

## Suggested Payment Status Model

Status pembayaran yang disarankan:

- `unpaid`
- `dp_waiting_payment`
- `dp_pending_verification`
- `dp_verified`
- `final_waiting_payment`
- `final_pending_verification`
- `final_verified`
- `expired`
- `refunded`

Catatan:

- Payment status dan booking status boleh dipisah agar logika slot, verifikasi admin, dan pelunasan lebih mudah dirawat.
- Jangan otomatis mengubah booking menjadi active hanya karena bukti pembayaran diupload.

## Development Notes

Local PHP path yang digunakan pada environment ini:

```powershell
C:\laragon\bin\php\php-8.3.29-Win32-vs16-x64\php.exe
```

Contoh menjalankan artisan dengan PHP Laragon:

```powershell
& "C:\laragon\bin\php\php-8.3.29-Win32-vs16-x64\php.exe" artisan view:cache
```

Saat membuat task berikutnya, baca README ini terlebih dahulu sebelum menentukan migrasi, model, enum/status, controller flow, tampilan public, atau halaman admin.
