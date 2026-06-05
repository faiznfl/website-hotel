<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Terpadu - Rumah RB</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333;
        }

        .kop-surat {
            width: 100%;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .hotel-name {
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
        }

        .hotel-address {
            font-size: 11px;
            color: #555;
            text-align: center;
        }

        .judul-laporan {
            text-align: center;
            margin-bottom: 20px;
        }

        .judul-laporan h3 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
        }

        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table-data th,
        .table-data td {
            border: 1px solid #ddd;
            padding: 6px;
        }

        .table-data th {
            background-color: #f4f4f4;
            text-transform: uppercase;
            font-size: 9px;
        }

        .section-title {
            background-color: #2C3E50;
            color: #ffffff;
            padding: 6px;
            font-weight: bold;
            margin-top: 10px;
            border: 1px solid #ddd;
            text-transform: uppercase;
            font-size: 10px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-bold {
            font-weight: bold;
        }

        /* --- STATUS UTILITY COLORS --- */
        .status-lunas {
            color: #155724;
        }

        .status-checkout {
            color: #004085; /* Warna Biru Navy untuk membedakan dengan Confirmed Green */
        }

        .status-pending {
            color: #856404;
        }

        .status-batal {
            color: #721c24;
        }

        .signature-container {
            margin-top: 30px;
            width: 100%;
        }

        .signature {
            float: right;
            width: 200px;
            text-align: center;
        }

        .signature-space {
            height: 60px;
        }
    </style>
</head>

<body>

    <div class="kop-surat">
        <div class="hotel-name">HOTEL & RESTO RUMAH RB</div>
        <div class="hotel-address">
            Jl. Contoh Alamat No. 123, Kota Tangerang Selatan, Banten<br>
            Telp: (021) 1234567 | info@hotelrumahrb.com
        </div>
    </div>

    <div class="judul-laporan">
        <h3>Laporan Transaksi {{ ucfirst($kategori) }}</h3>
        <p>Periode: <b>{{ \Carbon\Carbon::parse($awal)->translatedFormat('d F Y') }}</b> s/d
            <b>{{ \Carbon\Carbon::parse($akhir)->translatedFormat('d F Y') }}</b>
        </p>
    </div>

    @php $grand_total_semua = 0; @endphp

    {{-- SECTION A: DATA HOTEL --}}
    @if(($kategori === 'hotel' || $kategori === 'semua') && count($bookings) > 0)
        <div class="section-title">A. DATA RESERVASI HOTEL</div>
        <table class="table-data">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">Kode</th>
                    <th>Nama Tamu</th>
                    <th style="width: 15%;">Check In</th>
                    <th style="width: 15%;">Check Out</th>
                    <th style="width: 15%;">Total Harga</th>
                    <th style="width: 15%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $index => $b)
                    {{-- FIX LOGIKA: confirmed DAN checked_out dimasukkan ke dalam hitungan --}}
                    @php 
                        if (in_array($b->status, ['confirmed', 'checked_out'])) {
                            $grand_total_semua += $b->total_harga; 
                        }
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $b->kode_booking }}</td>
                        <td>{{ $b->nama_tamu }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($b->check_in)->format('d/m/Y') }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($b->check_out)->format('d/m/Y') }}</td>
                        <td class="text-right">Rp {{ number_format($b->total_harga, 0, ',', '.') }}</td>
                        
                        {{-- COLOR CLASS ADJUSTMENT --}}
                        <td class="text-center text-bold 
                            {{ $b->status == 'confirmed' ? 'status-lunas' : '' }}
                            {{ $b->status == 'checked_out' ? 'status-checkout' : '' }}
                            {{ $b->status == 'pending' ? 'status-pending' : '' }}
                            {{ $b->status == 'cancelled' ? 'status-batal' : '' }}">
                            
                            {{-- Formatting label agar enak dibaca (contoh: Checked Out) --}}
                            {{ str_replace('_', ' ', ucfirst($b->status)) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- SECTION B: DATA RESTORAN --}}
    @if(($kategori === 'restoran' || $kategori === 'semua') && count($orders) > 0)
        <div class="section-title">B. DATA PESANAN RESTORAN</div>
        <table class="table-data">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">ID Order</th>
                    <th>Customer</th>
                    <th style="width: 15%;">Tanggal</th>
                    <th style="width: 15%;">Lokasi/Kamar</th>
                    <th style="width: 15%;">Total Harga</th>
                    <th style="width: 15%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $index => $o)
                    @php 
                        if ($o->status_pembayaran == 'Lunas') {
                            $grand_total_semua += $o->total_harga; 
                        }
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">FOOD-{{ $o->id }}</td>
                        <td>{{ $o->nama_pemesan }}</td>
                        <td class="text-center">{{ $o->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-center">{{ $o->info_pemesan }}</td>
                        <td class="text-right">Rp {{ number_format($o->total_harga, 0, ',', '.') }}</td>
                        <td class="text-center text-bold {{ $o->status_pembayaran == 'Lunas' ? 'status-lunas' : '' }}">
                            {{ $o->status_pembayaran }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- RINGKASAN TOTAL --}}
    <table class="table-data" style="margin-top: 10px;">
        <tr>
            <td class="text-right text-bold" style="width: 70%; padding: 10px; background-color: #f9f9f9;">
                TOTAL PENDAPATAN BERSIH (CONFIRMED / CHECKED OUT / LUNAS):
            </td>
            <td class="text-right text-bold" style="font-size: 14px; padding: 10px; background-color: #27AE60; color: white;">
                Rp {{ number_format($grand_total_semua, 0, ',', '.') }}
            </td>
        </tr>
    </table>

    <div class="signature-container">
        <div class="signature">
            <p>Tangerang Selatan, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p>Admin Operasional,</p>
            <div class="signature-space"></div>
            <p class="text-bold">____________________</p>
            <p>Manajer Rumah RB</p>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>

</html>