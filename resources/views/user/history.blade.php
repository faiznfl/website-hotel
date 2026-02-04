@extends('layouts.main')

@section('title', 'Riwayat Reservasi - Hotel Rumah RB')

@section('content')
    <div class="bg-gray-50 min-h-screen py-12 pt-16">
        <div class="max-w-screen-xl mx-auto px-4">

            {{-- HEADER --}}
            <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Riwayat Reservasi</h1>
                    <p class="text-gray-500 mt-1">Pantau status pesanan dan liburan Anda di sini.</p>
                </div>
                <a href="{{ route('rooms.index') }}" class="bg-gray-900 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-yellow-500 hover:text-black transition-all shadow-lg flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Pesan Kamar Baru
                </a>
            </div>

            {{-- DAFTAR BOOKING --}}
            @if($bookings->isEmpty())
                {{-- Tampilan Kosong --}}
                <div class="bg-white rounded-3xl p-16 text-center shadow-lg border border-gray-100">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-300">
                        <i class="fa-solid fa-suitcase-rolling text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Reservasi</h3>
                    <p class="text-gray-500 mb-8 max-w-md mx-auto">Anda belum pernah melakukan pemesanan kamar.</p>
                    <a href="{{ route('rooms.index') }}" class="bg-yellow-500 text-gray-900 px-8 py-3 rounded-full font-bold hover:bg-yellow-400 transition shadow-lg hover:shadow-yellow-500/20 transform hover:-translate-y-1 inline-block">
                        Cari Kamar Sekarang
                    </a>
                </div>
            @else
                {{-- Ada Data Booking --}}
                <div class="grid gap-6">
                    @foreach($bookings as $booking)
                        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:border-yellow-200 transition duration-300 relative overflow-hidden group">
                            
                            {{-- Warna Status Kiri (Border Accent) --}}
                            <div class="absolute left-0 top-0 bottom-0 w-1.5 
                                {{ $booking->status == 'confirmed' ? 'bg-green-500' : '' }}
                                {{ $booking->status == 'pending' ? 'bg-yellow-500' : '' }}
                                {{ $booking->status == 'cancelled' ? 'bg-red-500' : '' }}">
                            </div>

                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 pl-4">
                                {{-- KIRI: INFO TANGGAL & KAMAR --}}
                                <div class="flex gap-4 md:gap-6 items-center w-full md:w-auto">
                                    
                                    {{-- Kotak Tanggal --}}
                                    <div class="text-center bg-gray-50 p-3 md:p-4 rounded-2xl border border-gray-100 min-w-[80px] md:min-w-[90px] group-hover:bg-yellow-50 transition flex-shrink-0">
                                        <span class="block text-2xl md:text-3xl font-black text-gray-900">
                                            {{ \Carbon\Carbon::parse($booking->check_in)->format('d') }}
                                        </span>
                                        <span class="block text-[10px] md:text-xs uppercase text-gray-500 font-bold group-hover:text-yellow-700">
                                            {{ \Carbon\Carbon::parse($booking->check_in)->format('M Y') }}
                                        </span>
                                    </div>

                                    {{-- Detail Kamar --}}
                                    <div class="flex-1 min-w-0"> {{-- min-w-0 agar truncate berfungsi --}}
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="bg-gray-100 text-gray-500 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider">
                                                #{{ $booking->kode_booking }}
                                            </span>
                                            
                                            @if($booking->status == 'pending')
                                                <span class="text-[10px] bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded font-bold uppercase animate-pulse">Menunggu Pembayaran</span>
                                            @elseif($booking->status == 'confirmed')
                                                <span class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded font-bold uppercase">Lunas</span>
                                            @else
                                                <span class="text-[10px] bg-red-100 text-red-700 px-2 py-0.5 rounded font-bold uppercase">Dibatalkan</span>
                                            @endif
                                        </div>
                                        
                                        <h3 class="text-lg md:text-xl font-bold text-gray-900 group-hover:text-yellow-600 transition mb-1 truncate">
                                            {{ $booking->kamar ? $booking->kamar->tipe_kamar : 'Tipe Kamar Dihapus' }}
                                        </h3>
                                        
                                        <div class="text-sm text-gray-500 flex flex-wrap items-center gap-x-4 gap-y-1">
                                            <span class="font-bold text-gray-900">Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</span>
                                            <span class="hidden sm:inline">•</span>
                                            <span class="text-xs sm:text-sm">{{ $booking->jumlah_kamar }} Unit</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- KANAN: TOMBOL AKSI --}}
                                <div class="flex flex-row md:flex-col gap-2 w-full md:w-40 mt-2 md:mt-0">
                                    <a href="{{ route('booking.show', $booking->id) }}" 
                                       class="flex-1 text-center border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900 px-4 py-2 rounded-xl text-xs md:text-sm font-bold transition">
                                        Detail
                                    </a>

                                    @if($booking->status == 'pending')
                                        <a href="https://wa.me/6281363374155?text=Halo%20Admin,%20saya%20mau%20konfirmasi%20pembayaran%20untuk%20Booking%20ID:%20{{ $booking->kode_booking }}" 
                                           target="_blank" 
                                           class="flex-1 text-center bg-green-600 text-white hover:bg-green-700 px-4 py-2 rounded-xl text-xs md:text-sm font-bold transition shadow-md hover:shadow-green-200">
                                           <i class="fa-brands fa-whatsapp"></i> Bayar
                                        </a>
                                        
                                        <form action="{{ route('booking.cancel', $booking->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?');" class="flex-1">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="w-full text-center bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-700 px-4 py-2 rounded-xl text-xs md:text-sm font-bold transition">
                                                Batal
                                            </button>
                                        </form>
                                    @endif
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection