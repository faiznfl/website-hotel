@extends('layouts.main')

@section('content')
<div class="py-12 bg-gray-50 min-h-screen font-sans">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- TOMBOL KEMBALI --}}
        <a href="{{ route('booking.history') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-yellow-600 mb-6 transition print:hidden">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Riwayat
        </a>

        {{-- AREA PRINT --}}
        <div id="printableArea" class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 relative text-gray-800">
            
            {{-- HEADER (Hitam & Emas Sesuai Tema) --}}
            <div class="bg-gray-900 px-8 py-6 text-white flex justify-between items-center print-header">
                <div class="flex items-center gap-4">
                    {{-- Icon Hotel (Emas) --}}
                    <div class="bg-white/10 p-2 rounded-lg border border-white/10 text-yellow-500">
                        <i class="fa-solid fa-hotel text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold tracking-wide text-white uppercase">E-TIKET / INVOICE</h1>
                        <p class="text-gray-400 text-xs mt-0.5">Hotel Rumah RB Padang</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-gray-400 text-xs uppercase mb-1">Status Booking</p>
                    @if($booking->status == 'confirmed')
                        <span class="bg-white text-gray-900 px-3 py-1 rounded text-xs font-bold uppercase tracking-wider border border-white">
                            Confirmed
                        </span>
                    @elseif($booking->status == 'pending')
                        <span class="bg-yellow-500 text-yellow-900 px-3 py-1 rounded text-xs font-bold uppercase tracking-wider border border-yellow-500">
                            Menunggu
                        </span>
                    @else
                        <span class="bg-red-500 text-white px-3 py-1 rounded text-xs font-bold uppercase tracking-wider">
                            Dibatalkan
                        </span>
                    @endif
                </div>
            </div>

            {{-- ISI KONTEN --}}
            <div class="p-8">
                
                {{-- INFO UTAMA --}}
                <div class="flex justify-between border-b border-gray-200 pb-6 mb-6">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold mb-1">Kode Booking</p>
                        <p class="text-2xl font-mono font-bold text-gray-900 tracking-tight">
                            {{ $booking->kode_booking ?? '#' . $booking->id }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500 uppercase font-bold mb-1">Tanggal Transaksi</p>
                        <p class="font-bold text-gray-700 text-sm">
                            {{ $booking->created_at->format('d F Y, H:i') }} WIB
                        </p>
                    </div>
                </div>

                {{-- DATA TAMU (Border Kiri Emas) --}}
                <div class="mb-6">
                    <h3 class="text-sm font-bold text-gray-900 uppercase mb-3 border-l-4 border-yellow-500 pl-3">
                        Informasi Tamu
                    </h3>
                    <div class="grid grid-cols-2 gap-6 bg-gray-50 p-4 rounded-lg border border-gray-100 print-bg-gray">
                        <div>
                            <p class="text-xs text-gray-500 uppercase mb-1">Nama Lengkap</p>
                            <p class="text-gray-900 font-bold text-sm">{{ $booking->nama_tamu }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase mb-1">Nomor WhatsApp</p>
                            <p class="text-gray-900 font-bold text-sm">{{ $booking->nomor_hp }}</p>
                        </div>
                    </div>
                </div>

                {{-- DETAIL KAMAR (Tabel Simple) --}}
                <div class="mb-6">
                    <h3 class="text-sm font-bold text-gray-900 uppercase mb-3 border-l-4 border-yellow-500 pl-3">
                        Detail Kamar
                    </h3>
                    <table class="w-full text-sm text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 text-gray-600 uppercase text-xs print-bg-gray">
                                <th class="p-3 border border-gray-200">Tipe Kamar</th>
                                <th class="p-3 border border-gray-200 text-center">Check In</th>
                                <th class="p-3 border border-gray-200 text-center">Check Out</th>
                                <th class="p-3 border border-gray-200 text-center">Jml</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="p-3 border border-gray-200 font-bold text-gray-900">
                                    {{ $booking->kamar->tipe_kamar }}
                                </td>
                                <td class="p-3 border border-gray-200 text-center text-gray-700">
                                    {{ \Carbon\Carbon::parse($booking->check_in)->format('d M Y') }}
                                </td>
                                <td class="p-3 border border-gray-200 text-center text-gray-700">
                                    {{ \Carbon\Carbon::parse($booking->check_out)->format('d M Y') }}
                                </td>
                                <td class="p-3 border border-gray-200 text-center font-bold text-gray-900">
                                    {{ $booking->jumlah_kamar }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- FOOTER / CATATAN (Update Baru) --}}
                <div class="bg-yellow-50 text-yellow-900 p-4 rounded-lg text-xs flex items-start gap-3 mt-8 print-bg-yellow">
                    <i class="fa-solid fa-circle-info mt-0.5 text-yellow-600"></i>
                    <div>
                        <p class="font-bold mb-1 text-yellow-800">Catatan Penting:</p>
                        <ul class="list-disc pl-4 space-y-1 text-yellow-800/80">
                            {{-- POIN PENTING YANG DIMINTA --}}
                            <li class="font-bold">Dokumen ini valid baik dicetak (Print) maupun Digital (tunjukkan layar HP).</li>
                            <li>Waktu Check-in: 14:00 WIB | Check-out: 12:00 WIB.</li>
                            <li>Harap membawa kartu identitas asli (KTP/SIM) saat kedatangan.</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        {{-- TOMBOL PRINT --}}
        <div class="mt-8 text-center print:hidden">
            <button onclick="printInvoice()" class="bg-gray-900 text-white px-8 py-3 rounded-full shadow-lg hover:bg-yellow-600 transition font-bold text-sm inline-flex items-center gap-2 transform hover:-translate-y-1">
                <i class="fa-solid fa-print"></i> Cetak / Simpan PDF
            </button>
        </div>

    </div>
</div>

<script>
    function printInvoice() {
        var originalContents = document.body.innerHTML;
        var printContents = document.getElementById('printableArea').innerHTML;
        
        document.body.innerHTML = printContents;
        window.print();
        
        document.body.innerHTML = originalContents;
        window.location.reload(); 
    }
</script>

<style>
    @media print {
        /* 1. Reset Margin Kertas */
        @page {
            size: auto;
            margin: 0mm;
        }

        body {
            background-color: white;
            margin: 0;
            font-family: sans-serif;
        }

        nav, footer, .print\:hidden { display: none !important; }

        /* 2. Styling Kontainer Print */
        #printableArea {
            width: 100%;
            border: none !important;
            box-shadow: none !important;
            padding: 20mm !important; 
            box-sizing: border-box;
        }

        /* 3. Paksa Warna Background Muncul */
        .print-header {
            background-color: #111827 !important; /* Gray-900 */
            color: white !important;
            -webkit-print-color-adjust: exact;
        }
        
        .print-bg-gray {
            background-color: #f9fafb !important;
            -webkit-print-color-adjust: exact;
        }

        .print-bg-yellow {
            background-color: #fefce8 !important; /* Yellow-50 */
            -webkit-print-color-adjust: exact;
        }

        .border-yellow-500 {
            border-color: #eab308 !important;
            -webkit-print-color-adjust: exact;
        }
        
        .text-yellow-500 {
            color: #eab308 !important;
            -webkit-print-color-adjust: exact;
        }
    }
</style>
@endsection