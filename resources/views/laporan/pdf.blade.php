<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Reservasi - Hotel Rumah RB</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333;
        }

        /* KOP SURAT */
        .kop-surat {
            width: 100%;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .kop-surat table {
            width: 100%;
            border: none;
        }

        .kop-surat td {
            border: none;
            text-align: center;
        }

        .hotel-name {
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }

        .hotel-address {
            font-size: 12px;
            color: #555;
        }

        /* JUDUL LAPORAN */
        .judul-laporan {
            text-align: center;
            margin-bottom: 20px;
        }

        .judul-laporan h3 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
        }

        .judul-laporan p {
            margin: 5px 0 0 0;
            font-size: 12px;
        }

        /* TABEL DATA */
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table-data th,
        .table-data td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .table-data th {
            background-color: #f4f4f4;
            color: #333;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }

        .table-data tbody tr:nth-child(even) {
            background-color: #fdfdfd;
        }

        /* UTILITIES */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-bold {
            font-weight: bold;
        }

        /* WARNA STATUS */
        .status {
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }

        .status-confirmed {
            color: #155724;
        }

        .status-pending {
            color: #856404;
        }

        .status-cancelled {
            color: #721c24;
        }

        /* TANDA TANGAN */
        .signature-container {
            width: 100%;
            margin-top: 40px;
        }

        .signature {
            float: right;
            width: 250px;
            text-align: center;
        }

        .signature p {
            margin: 0;
            padding: 2px 0;
        }

        .signature-space {
            height: 70px;
            /* Jarak untuk tanda tangan asli */
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="kop-surat">
        <table>
            <tr>
                <td>
                    <div class="hotel-name">HOTEL RUMAH RB</div>
                    <div class="hotel-address">
                        Jl. Contoh Alamat Skripsi No. 123, Kota Tangerang Selatan, Banten<br>
                        Telp: (021) 1234567 | Email: info@hotelrumahrb.com | Website: www.hotelrumahrb.com
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="judul-laporan">
        <h3>Laporan Data Reservasi</h3>
        <p>Periode: <b>{{ \Carbon\Carbon::parse($awal)->translatedFormat('d F Y') }}</b> s/d
            <b>{{ \Carbon\Carbon::parse($akhir)->translatedFormat('d F Y') }}</b></p>
    </div>

    <table class="table-data">
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="12%">Kode Booking</th>
                <th width="16%">Nama Tamu</th>
                <th width="12%">No. HP</th>
                <th width="10%">Check In</th>
                <th width="10%">Check Out</th>
                <th width="15%">Total Harga</th>
                <th width="12%">Status</th>
            </tr>
        </thead>
        <tbody>
            @php $total_pendapatan = 0; @endphp

            @forelse($bookings as $index => $b)
                @php
    // Hitung total harga hanya untuk yang statusnya confirmed
    if ($b->status == 'confirmed') {
        $total_pendapatan += $b->total_harga;
    }
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $b->kode_booking ?? '-' }}</td>
                    <td>{{ $b->nama_tamu }}</td>
                    <td class="text-center">{{ $b->nomor_hp }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($b->check_in)->format('d/m/Y') }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($b->check_out)->format('d/m/Y') }}</td>
                    <td class="text-right">Rp {{ number_format($b->total_harga, 0, ',', '.') }}</td>
                    <td class="text-center text-bold">
                        @if($b->status == 'confirmed')
                            <span class="status status-confirmed">Confirmed</span>
                        @elseif($b->status == 'pending')
                            <span class="status status-pending">Pending</span>
                        @else
                            <span class="status status-cancelled">Cancelled</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px;">Tidak ada data reservasi pada periode tanggal
                        ini.</td>
                </tr>
            @endforelse
        </tbody>

        @if(count($bookings) > 0)
            <tfoot>
                <tr>
                    <td colspan="6" class="text-right text-bold" style="padding: 10px;">Total Pendapatan (Confirmed):</td>
                    <td colspan="2" class="text-bold text-left" style="background-color: #f4f4f4; padding: 10px;">
                        Rp {{ number_format($total_pendapatan, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="signature-container">
        <div class="signature">
            <p>Tangerang Selatan, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p>Mengetahui,</p>
            <div class="signature-space"></div>
            <p class="signature-name">Nama Manajer Hotel</p>
            <p>General Manager</p>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>

</html>
