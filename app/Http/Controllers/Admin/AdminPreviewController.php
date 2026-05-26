<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminPreviewController extends Controller
{
    public function dashboard()
    {
        return $this->render('Dashboard', 'Ringkasan operasional harian, booking baru, dan alert pembayaran.');
    }

    public function bookingRequests()
    {
        return $this->render('Booking Requests', 'Review request baru sebelum customer melakukan pembayaran DP.');
    }

    public function bookingsActive()
    {
        return $this->render('Bookings Active', 'Daftar booking yang sudah DP verified dan mengunci slot.');
    }

    public function bookingDetail(string $booking)
    {
        return $this->render('Booking Detail', 'Halaman detail booking untuk approval, verifikasi, dan tindak lanjut.', [
            'bookingCode' => strtoupper($booking),
        ]);
    }

    public function calendar()
    {
        return $this->render('Calendar & Slots', 'Monitoring kapasitas harian, sesi pagi-siang, dan sesi sore-malam.');
    }

    public function dpVerification()
    {
        return $this->render('DP Verification', 'Verifikasi manual konfirmasi DP sebelum booking menjadi aktif.');
    }

    public function finalPayment()
    {
        return $this->render('Final Payment', 'Verifikasi pelunasan maksimal H-1 acara dan update status booking.');
    }

    public function pricingReviews()
    {
        return $this->render('Pricing Review', 'Hitung harga final dan breakdown biaya tambahan berdasarkan lokasi.');
    }

    public function packages()
    {
        return $this->render('Packages', 'Kelola paket, base price, dan perbedaan ketentuan wedding/non-wedding.');
    }

    public function locationRules()
    {
        return $this->render('Location Rules', 'Kelola kategori lokasi dan pola estimasi biaya tambahan.');
    }

    public function reschedules()
    {
        return $this->render('Reschedule Requests', 'Review request reschedule manual berdasarkan ketersediaan jadwal.');
    }

    public function cancellations()
    {
        return $this->render('Cancellations', 'Catat pembatalan dan status DP non-refundable.');
    }

    public function forceMajeure()
    {
        return $this->render('Force Majeure', 'Penanganan fotografer pengganti, cuaca buruk, dan refund kondisi ekstrem.');
    }

    public function customers()
    {
        return $this->render('Customers', 'Riwayat customer dan histori booking berdasarkan nomor WhatsApp.');
    }

    public function settings()
    {
        return $this->render('Settings', 'Konfigurasi rekening transfer, WhatsApp utama, dan policy operasional.');
    }

    protected function render(string $pageTitle, string $pageSummary, array $extra = [])
    {
        return view('pages.admin.preview', array_merge([
            'pageTitle' => $pageTitle,
            'pageSummary' => $pageSummary,
            'title' => $pageTitle . ' - Etherno Admin',
        ], $extra));
    }
}
