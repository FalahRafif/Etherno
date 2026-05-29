FEATURE AUDIT MATRIX — BOOKING MODULE
✅ KATEGORI A — Sudah Terimplementasi Penuh
File ada, logic lengkap, view ada, route terdaftar, data mengalir end-to-end.
#	Fitur	File Utama
A1	Public: Submit booking request	Api/Public/BookingController@store + GuestBookingService + StoreBookingRequest + bookingpage.blade.php + form.js
A2	Public: Check slot availability	Api/Public/BookingController@availability + GuestBookingService@getDateAvailability
A3	Public: Location options cascade	Api/Public/BookingController@locationOptions + GuestBookingService@getLocationOptions
A4	Public: Price estimate	Api/Public/BookingController@estimate + GuestBookingService@getPriceEstimate
A5	Public: Booking status lookup	Api/Public/BookingController@statusLookup + GuestBookingService@getBookingStatusPayload + status.blade.php + status.js
A6	Public: Upload payment proof (via status page)	Api/Public/BookingController@uploadPaymentProof + GuestBookingService@uploadPaymentProof + status.js
A7	Public: Submission proof PDF	GuestBookingService@ensureSubmissionProofDocument + submission-proof-pdf.blade.php
A8	Public: Download submission proof	Web/Public/BookingSupportController@downloadSubmissionProof
A9	Public: Success page	Web/Public/BookingSupportController@success + success.blade.php
A10	Public: Booking form page	Web/Public/LandingPageController@booking + bookingpage.blade.php
A11	Public: Packages page	Web/Public/LandingPageController@packages + GuestPackageService + packages-page/index.blade.php
A12	Public: About page	Web/Public/LandingPageController@aboutEtherno + about-page/etherno.blade.php
A13	Public: Cancellation policy page	BookingSupportController@cancellationPolicy + cancellation-policy.blade.php
A14	Admin: Booking list (dynamic)	AdminPreviewController@bookingsList + BookingListService + bookings/list.blade.php
A15	Admin: Booking detail (dynamic)	AdminPreviewController@bookingDetail + BookingDetailService + bookings/detail.blade.php
A16	Admin: Calendar (dynamic)	AdminPreviewController@calendar + BookingCalendarService + calendar.blade.php + calendar-booking.js
A17	Admin: Calendar events JSON	AdminPreviewController@calendarEvents + BookingCalendarService@getCalendarEvents
A18	Admin: Approve booking	BookingDetailController@approve + BookingDetailService@approveBooking
A19	Admin: Reject booking	BookingDetailController@reject + BookingDetailService@rejectBooking
A20	Admin: Upload payment manual	BookingDetailController@uploadPayment + BookingDetailService@uploadManualPayment
A21	Admin: Verify DP	BookingDetailController@verifyDp + BookingDetailService@verifyDp
A22	Admin: Reject manual	BookingDetailController@rejectManual + BookingDetailService@rejectManual
A23	Admin: Verify final payment	BookingDetailController@verifyFinalPayment + BookingDetailService@verifyFinalPayment
A24	Admin: Cancel booking	BookingDetailController@cancelBooking + BookingDetailService@cancelBooking
A25	Admin: Complete booking	BookingDetailController@completeBooking + BookingDetailService@completeBooking
A26	Admin: Force majeure (reschedule + refund)	BookingDetailController@forceMajeure + BookingDetailService@forceMajeureReschedule/Refund
A27	Admin: Upload refund proof	BookingDetailController@uploadRefundProof + BookingDetailService@uploadRefundProof
A28	Admin: Store billing detail	BookingDetailController@storeBillingDetail + BookingDetailService@addBillingDetail
A29	Admin: Generate installment	BookingDetailController@storeInstallment + BookingDetailService@generateInstallment
⚠️ KATEGORI B — Terimplementasi Sebagian (Partial)
Ada view/controller tetapi menampilkan hardcoded sample data — belum terhubung ke live data dari service/database.
#	Fitur	File
B1	Admin: Dashboard	dashboard.blade.php + AdminPreviewController@dashboard
B2	Admin: Booking requests queue	bookings/requests.blade.php + AdminPreviewController@bookingRequests
B3	Admin: Active bookings	bookings/active.blade.php + AdminPreviewController@bookingsActive
B4	Admin: DP verification queue	payments/dp.blade.php + AdminPreviewController@dpVerification
B5	Admin: Final payment verification	payments/final.blade.php + AdminPreviewController@finalPayment
B6	Admin: Pricing reviews	pricing/reviews.blade.php + AdminPreviewController@pricingReviews
B7	Admin: Reschedules list	reschedules.blade.php + AdminPreviewController@reschedules
B8	Admin: Cancellations list	cancellations.blade.php + AdminPreviewController@cancellations
B9	Admin: Force majeure list	force-majeure.blade.php + AdminPreviewController@forceMajeure
B10	Admin: Customers list	customers.blade.php + AdminPreviewController@customers
B11	Admin: Settings page	settings.blade.php + AdminPreviewController@settings
B12	Public: DP confirmation page	payment-dp.blade.php
B13	Public: Final payment confirmation page	payment-final.blade.php
B14	Public: Reschedule request page	reschedule.blade.php
🔧 KATEGORI C — Ada Struktur, Logic Belum Ada (Stub/Skeleton)
Tidak ada. Semua yang ada file-nya sudah punya logic.
#	Fitur
—	Tidak ditemukan
❌ KATEGORI D — Belum Ada Sama Sekali (Missing)
Tidak ada. Semua yang disebutkan di README sudah ada filenya.
#	Fitur	Referensi README
—	Tidak ditemukan	—
IMPLEMENTATION PLAN
Temuan Kunci
Core booking flow end-to-end sudah berfungsi penuh:
Customer submit → Admin list/detail → Approve (auto billing) → 
Customer upload DP → Admin verify DP → Admin verify final → Complete
Yang belum selesai adalah 14 halaman "preview" yang sudah punya view lengkap (HTML/Blade) tapi masih menampilkan hardcoded sample data alih-alih data live dari database.
Rencana Implementasi Berurutan
Priority P1 — Core Operational Queue (Admin melihat booking nyata)
Tanpa ini, admin tidak bisa melihat booking yang perlu diproses dari pages dedicated.
Step	Fitur	Pendekatan
1	B2: Booking requests queue	Buat BookingListService@getRequestsPayload() — filter BS_WAITING_APPROVAL. Update controller pass data. Update view ganti hardcoded → dynamic.
2	B4: DP verification queue	Buat BookingListService@getDpVerificationPayload() — filter BS_APPROVED_WAITING_DP. Sama pattern.
3	B5: Final payment verification	Buat BookingListService@getFinalPaymentPayload() — filter BS_APPROVED_WAITING_FINAL_PAYMENT.
Priority P2 — Admin Monitoring Pages
Pages yang menampilkan status booking untuk monitoring operasional.
Step	Fitur
4	B3: Active bookings
5	B7: Reschedules list
6	B8: Cancellations list
7	B9: Force majeure list
8	B1: Dashboard stats
Priority P3 — Enhancement Pages
Pages yang menunjang tapi tidak menghalangi operasional.
Step	Fitur
9	B6: Pricing reviews
10	B10: Customers list
11	B11: Settings page
12	B12/B13/B14: Public support forms
Pendekatan Teknis
Semua 11 admin pages (B1–B11) akan menggunakan pola yang sama:
1. Tambah method di BookingListService — satu method per page, return array $rows, $stats, $sideCards.
2. Update AdminPreviewController — pass data dari service ke view.
3. Update view — ganti blok PHP hardcoded → variabel dinamis dari controller.
Alasan pakai BookingListService (bukan service baru): service ini sudah punya pattern query booking dengan filter status, eager loading relation, dan formatting row. Cukup tambah method specialized per page.
# Todos
[✓] FASE 1.1: Baca README.md
[✓] FASE 1.2: Scan kodebase aktual (views, config, dll)
[✓] FASE 1.3: Buat Feature Audit Matrix
[✓] FASE 1.4: Presentasikan Audit Matrix + Implementation Plan
[ ] FASE 2: Implementasi (menunggu konfirmasi)
STOP — FASE 1 selesai. Menunggu konfirmasi sebelum masuk FASE 2.
Ringkasan:
- 29 fitur sudah terimplementasi penuh (Kategori A) — termasuk seluruh core booking flow end-to-end
- 14 fitur terimplementasi sebagian (Kategori B) — 11 admin preview pages + 3 public support forms yang masih hardcoded
- 0 stub, 0 missing
- Implementation plan: 12 steps, dari P1 (operational queue) → P2 (monitoring) → P3 (enhancement)
Silakan konfirmasi apakah saya bisa lanjut ke FASE 2 — Implementasi sesuai plan di atas, atau ada prioritas yang ingin diubah/ditambahkan.