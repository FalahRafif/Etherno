<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Pengajuan Booking - {{ $booking_case_id }}</title>
    <style>
        @page {
            size: A4;
            margin: 14mm 14mm 12mm;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            line-height: 1.45;
            color: #1a1916;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .doc-header {
            margin-bottom: 10px;
            border-bottom: 2px solid #b58f42;
            padding-bottom: 8px;
        }

        .doc-header td {
            vertical-align: top;
        }

        .brand-wrap {
            width: 58%;
        }

        .meta-wrap {
            width: 42%;
            text-align: right;
        }

        .brand-table {
            width: auto;
        }

        .brand-table td {
            vertical-align: middle;
        }

        .logo-cell {
            width: 42px;
        }

        .logo {
            width: 34px;
            height: 34px;
            object-fit: contain;
        }

        .brand-name {
            font-family: DejaVu Serif, Times New Roman, serif;
            font-size: 20px;
            font-weight: 700;
            color: #1a1814;
            line-height: 1.1;
        }

        .brand-sub {
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #7a6025;
            margin-top: 2px;
        }

        .doc-label {
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: #888;
            margin-bottom: 2px;
        }

        .doc-value {
            font-size: 11px;
            font-weight: 700;
            color: #1a1916;
        }

        .hero-box {
            margin: 10px 0;
            padding: 10px 12px;
            background: #f7f3ea;
            border-left: 3px solid #b58f42;
        }

        .hero-title {
            font-family: DejaVu Serif, Times New Roman, serif;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 3px;
            color: #1a1814;
        }

        .hero-text {
            font-size: 10px;
            color: #56514a;
        }

        .summary-box {
            margin: 10px 0 12px;
            border: 1px solid #dfd6c3;
        }

        .summary-box td {
            padding: 7px 9px;
            border-right: 1px solid #dfd6c3;
            vertical-align: top;
        }

        .summary-box td:last-child {
            border-right: none;
        }

        .summary-value {
            font-size: 10px;
            font-weight: 700;
            color: #1a1814;
            word-break: break-word;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 7px;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #8a6823;
            background: #f4ebd4;
            border: 1px solid #e4d1a1;
        }

        .section {
            margin-bottom: 10px;
        }

        .section-title {
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #7a6025;
            border-bottom: 1px solid #e3d5b4;
            padding-bottom: 3px;
            margin-bottom: 5px;
        }

        .info-grid td {
            width: 50%;
            padding: 4px 8px 4px 0;
            vertical-align: top;
        }

        .full-row {
            width: 100% !important;
        }

        .field-label {
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #888;
            margin-bottom: 1px;
        }

        .field-value {
            font-size: 10px;
            font-weight: 600;
            color: #1a1916;
            word-break: break-word;
        }

        .field-value.muted {
            color: #5d5850;
            font-weight: 500;
        }

        .next-steps {
            margin-top: 10px;
            padding: 8px 10px;
            background: #fbf7ee;
            border: 1px solid #e7d6ac;
        }

        .next-steps-title {
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: #7a6025;
            margin-bottom: 4px;
        }

        .next-steps ul {
            margin: 0;
            padding-left: 15px;
        }

        .next-steps li {
            margin-bottom: 2px;
            color: #56514a;
        }

        .next-steps strong {
            color: #1a1814;
        }

        .footer-note {
            margin-top: 12px;
            padding-top: 6px;
            border-top: 1px solid #e3d5b4;
            font-size: 8px;
            color: #8b8478;
        }
    </style>
</head>
<body>
    @php
        $logoPath = public_path('assets/etherno/public/icon_trans_2.png');
        $logoData = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';
    @endphp

    <table class="doc-header">
        <tr>
            <td class="brand-wrap">
                <table class="brand-table">
                    <tr>
                        @if($logoData !== '')
                            <td class="logo-cell">
                                <img src="{{ $logoData }}" alt="Etherno" class="logo">
                            </td>
                        @endif
                        <td>
                            <div class="brand-name">Etherno</div>
                            <div class="brand-sub">Wedding &amp; Event Documentation</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="meta-wrap">
                <div class="doc-label">Dokumen Diterbitkan</div>
                <div class="doc-value">{{ $submitted_at_label }}</div>
            </td>
        </tr>
    </table>

    <div class="hero-box">
        <div class="hero-title">Bukti Pengajuan Booking</div>
        <div class="hero-text">
            Dokumen ini adalah bukti bahwa pengajuan booking Anda telah diterima Etherno dan sedang menunggu review.
            Simpan <strong>Case ID</strong> untuk pengecekan status booking. Slot belum dianggap fix sampai pembayaran DP diverifikasi.
        </div>
    </div>

    <table class="summary-box">
        <tr>
            <td>
                <div class="doc-label">Case ID</div>
                <div class="summary-value">{{ $booking_case_id }}</div>
            </td>
            <td>
                <div class="doc-label">Waktu Pengajuan</div>
                <div class="summary-value">{{ $submitted_at_label }}</div>
            </td>
            <td>
                <div class="doc-label">Status</div>
                <div class="summary-value"><span class="status-badge">Menunggu Approval</span></div>
            </td>
        </tr>
    </table>

    <div class="section">
        <div class="section-title">Informasi Customer</div>
        <table class="info-grid">
            <tr>
                <td>
                    <div class="field-label">Nama Lengkap</div>
                    <div class="field-value">{{ $customer_name }}</div>
                </td>
                <td>
                    <div class="field-label">No WhatsApp</div>
                    <div class="field-value">{{ $customer_phone }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Jadwal &amp; Lokasi Acara</div>
        <table class="info-grid">
            <tr>
                <td>
                    <div class="field-label">Tanggal Acara</div>
                    <div class="field-value">{{ $booking_date_label }}</div>
                </td>
                <td>
                    <div class="field-label">Sesi Acara</div>
                    <div class="field-value">{{ $event_session }}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="field-label">Lokasi Acara</div>
                    <div class="field-value">{{ $location_label }}</div>
                </td>
                <td>
                    <div class="field-label">Patokan Pin Lokasi</div>
                    <div class="field-value muted">{{ $pin_address }}</div>
                </td>
            </tr>
            @if(trim($event_detail) !== '' && trim($event_detail) !== '-')
            <tr>
                <td colspan="2" class="full-row">
                    <div class="field-label">Detail Acara</div>
                    <div class="field-value muted">{!! nl2br(e($event_detail)) !!}</div>
                </td>
            </tr>
            @endif
        </table>
    </div>

    <div class="section">
        <div class="section-title">Paket Layanan</div>
        <table class="info-grid">
            <tr>
                <td>
                    <div class="field-label">Nama Paket</div>
                    <div class="field-value">{{ $package_name }}</div>
                </td>
                <td>
                    <div class="field-label">Tipe Paket</div>
                    <div class="field-value">{{ $package_type }}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="field-label">Harga Dasar Paket</div>
                    <div class="field-value">{{ $package_price }}</div>
                </td>
                <td>
                    <div class="field-label">ID Paket</div>
                    <div class="field-value muted">{{ $package_case_id }}</div>
                </td>
            </tr>
            @if(trim($package_address) !== '' && trim($package_address) !== '-')
            <tr>
                <td colspan="2" class="full-row">
                    <div class="field-label">Alamat Paket</div>
                    <div class="field-value muted">{{ $package_address }}</div>
                </td>
            </tr>
            @endif
        </table>
    </div>

    <div class="next-steps">
        <div class="next-steps-title">Langkah Selanjutnya</div>
        <ul>
            <li><strong>Review &amp; Approval:</strong> Tim Etherno akan mengecek data booking, kecocokan paket, dan ketersediaan tim.</li>
            <li><strong>Tagihan DP:</strong> Jika pengajuan disetujui, billing diinisialisasi dan tagihan DP akan dikirim melalui WhatsApp.</li>
            <li><strong>Pembayaran DP:</strong> DP maksimal dibayar <strong>3 hari setelah approval</strong>. Slot baru dianggap terkunci setelah DP diverifikasi.</li>
            <li><strong>Pelunasan:</strong> Setelah DP diverifikasi, tagihan pelunasan akan disiapkan. Pelunasan maksimal dilakukan <strong>H-1</strong> sebelum acara.</li>
            <li><strong>Reschedule &amp; Force Majeure:</strong> Jika ada perubahan jadwal atau kondisi khusus, semua tindak lanjut dilakukan melalui halaman <strong>Cek Status Booking</strong> dan konfirmasi WhatsApp.</li>
        </ul>
    </div>

    <div class="footer-note">
        Dokumen ini dihasilkan otomatis oleh sistem Etherno. Untuk cek status booking atau melanjutkan pembayaran, gunakan Case ID di atas pada halaman <strong>Cek Status Booking</strong>.
    </div>
</body>
</html>
