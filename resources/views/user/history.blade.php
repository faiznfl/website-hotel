@extends('layouts.main')

@section('content')
    <div class="bg-gray-50 min-h-screen py-12">
        <div class="max-w-screen-xl mx-auto px-4">

            {{-- Header --}}
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Riwayat Reservasi</h1>
                <a href="{{ url('/rooms') }}" class="text-yellow-600 font-bold hover:underline text-sm">
                    + Pesan Kamar Baru
                </a>
            </div>

            {{-- Pesan Sukses/Error --}}
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Berhasil!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if($bookings->isEmpty())
                {{-- Tampilan Kosong --}}
                <div class="bg-white rounded-2xl p-12 text-center shadow-sm border border-gray-100">
                    <div class="text-gray-200 mb-4">
                        <i class="fa-solid fa-calendar-xmark text-6xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Belum Ada Reservasi</h3>
                    <p class="text-gray-500 mb-6">Anda belum pernah melakukan pemesanan kamar di hotel kami.</p>
                    <a href="{{ url('/rooms') }}"
                        class="bg-gray-900 text-white px-6 py-3 rounded-xl font-bold hover:bg-yellow-600 transition">
                        Cari Kamar Sekarang
                    </a>
                </div>
            @else
                {{-- Daftar Booking --}}
                <div class="grid gap-6">
                    @foreach($bookings as $booking)
                        
                        {{-- 
                            BUNGKUS CARD DENGAN TAG <a> AGAR BISA DIKLIK 
                            (Menuju Halaman Detail/Invoice)
                        --}}
                        <a href="{{ route('booking.show', $booking->id) }}" class="group block relative">
                            
                            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center hover:shadow-lg hover:border-yellow-400 transition duration-300">
                                
                                {{-- Info Kiri (Tanggal & Kamar) --}}
                                <div class="flex gap-6 items-center">
                                    <div class="text-center bg-gray-50 p-4 rounded-xl border border-gray-100 min-w-[80px] group-hover:bg-yellow-50 transition">
                                        <span class="block text-2xl font-bold text-gray-900">
                                            {{ \Carbon\Carbon::parse($booking->check_in)->format('d') }}
                                        </span>
                                        <span class="block text-xs uppercase text-gray-500 font-bold group-hover:text-yellow-600">
                                            {{ \Carbon\Carbon::parse($booking->check_in)->format('M') }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="bg-gray-100 text-gray-600 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wide">
                                                {{ $booking->kode_booking }}
                                            </span>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-900 group-hover:text-yellow-600 transition">
                                            {{ $booking->kamar ? $booking->kamar->tipe_kamar : 'Tipe Kamar Dihapus' }}
                                        </h3>
                                        <p class="text-gray-500 text-sm mt-1">
                                            <i class="fa-solid fa-moon mr-1"></i> {{ $booking->jumlah_kamar }} Kamar
                                            <span class="mx-2">•</span>
                                            Check-out: {{ \Carbon\Carbon::parse($booking->check_out)->format('d M Y') }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Info Kanan (Status & Tombol Action) --}}
                                <div class="mt-4 md:mt-0 flex flex-col items-end gap-3 z-20 relative">

                                    {{-- 1. Status Badge --}}
                                    @if($booking->status == 'pending')
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 text-xs font-bold uppercase tracking-wide border border-yellow-200">
                                            <i class="fa-solid fa-clock"></i> Menunggu Konfirmasi
                                        </span>
                                    @elseif($booking->status == 'confirmed')
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold uppercase tracking-wide border border-green-200">
                                            <i class="fa-solid fa-circle-check"></i> Confirmed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-bold uppercase tracking-wide border border-red-200">
                                            <i class="fa-solid fa-circle-xmark"></i> Dibatalkan
                                        </span>
                                    @endif

                                    {{-- 2. TOMBOL BATALKAN (PENTING: Gunakan z-index tinggi agar bisa diklik terpisah) --}}
                                    @if($booking->status == 'pending')
                                        {{-- 
                                            Kita gunakan object tag atau div dengan onclick stopPropagation 
                                            agar saat tombol batal diklik, link utama (detail) TIDAK ikut terklik.
                                        --}}
                                        <div onclick="event.preventDefault();"> 
                                            <form action="{{ route('booking.cancel', $booking->id) }}" method="POST" 
                                                  onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?');">
                                                @csrf
                                                @method('PATCH')
                                                
                                                <button type="submit" class="bg-white border border-red-200 text-red-500 hover:bg-red-50 hover:text-red-700 px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1 shadow-sm">
                                                    <i class="fa-solid fa-trash-can"></i> Batalkan Pesanan
                                                </button>
                                            </form>
                                        </div>
                                    @endif

                                    {{-- 3. Waktu Pemesanan --}}
                                    <span class="text-xs text-gray-400">
                                        Dipesan: {{ $booking->created_at->diffForHumans() }}
                                    </span>
                                </div>

                                {{-- Panah Navigasi (Pemanis) --}}
                                <div class="absolute right-6 top-1/2 transform -translate-y-1/2 opacity-0 group-hover:opacity-10 md:opacity-0 transition-opacity">
                                    <i class="fa-solid fa-chevron-right text-6xl text-gray-900"></i>
                                </div>

                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection