<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Bukti Pengajuan Booking</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #1e1e1e;
            line-height: 1.55;
            margin: 24px;
        }
        .heading {
            margin-bottom: 18px;
            border-bottom: 1px solid #d8c18a;
            padding-bottom: 10px;
        }
        .title {
            font-size: 20px;
            margin: 0;
            color: #3a2b10;
        }
        .sub {
            margin: 6px 0 0;
            color: #5a4a28;
            font-size: 11px;
        }
        .meta {
            margin-top: 12px;
            padding: 10px;
            background: #fbf7ee;
            border: 1px solid #e7d7ae;
            border-radius: 6px;
        }
        .meta strong {
            color: #3a2b10;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
        }
        th, td {
            border: 1px solid #d9d9d9;
            vertical-align: top;
            padding: 8px 10px;
        }
        th {
            width: 210px;
            text-align: left;
            color: #4b3a1c;
            background: #f8f3e7;
            font-weight: 700;
        }
        .notes {
            margin-top: 16px;
            font-size: 11px;
            color: #4f4f4f;
            border-top: 1px dashed #cfcfcf;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="heading">
        <h1 class="title">Bukti Pengajuan Booking Etherno</h1>
        <p class="sub">Dokumen ini merupakan bukti pengajuan awal booking dan belum menjadi konfirmasi slot final.</p>
    </div>

    <div class="meta">
        <div><strong>Case ID:</strong> {{ $booking_case_id }}</div>
        <div><strong>Kode Request:</strong> {{ $request_code }}</div>
        <div><strong>Waktu Pengajuan:</strong> {{ $submitted_at_label }}</div>
    </div>

    <table>
        <tr>
            <th>Nama Customer</th>
            <td>{{ $customer_name }}</td>
        </tr>
        <tr>
            <th>No WhatsApp</th>
            <td>{{ $customer_phone }}</td>
        </tr>
        <tr>
            <th>Tanggal Acara</th>
            <td>{{ $booking_date_label }}</td>
        </tr>
        <tr>
            <th>Sesi Acara</th>
            <td>{{ $event_session }}</td>
        </tr>
        <tr>
            <th>Nama Paket</th>
            <td>{{ $package_name }}</td>
        </tr>
        <tr>
            <th>Case ID Paket</th>
            <td>{{ $package_case_id }}</td>
        </tr>
        <tr>
            <th>Tipe Paket</th>
            <td>{{ $package_type }}</td>
        </tr>
        <tr>
            <th>Harga Paket Dasar</th>
            <td>{{ $package_price }}</td>
        </tr>
        <tr>
            <th>Alamat Paket</th>
            <td>{{ $package_address }}</td>
        </tr>
        <tr>
            <th>Lokasi Acara</th>
            <td>{{ $location_label }}</td>
        </tr>
        <tr>
            <th>Link Pin Lokasi</th>
            <td>{{ $google_maps_pin }}</td>
        </tr>
        <tr>
            <th>Detail Acara</th>
            <td>{!! nl2br(e($event_detail)) !!}</td>
        </tr>
    </table>

    <p class="notes">
        Catatan: Tim Etherno akan melakukan review data terlebih dahulu. Slot dianggap terkunci setelah proses DP diverifikasi oleh admin.
    </p>
</body>
</html>
