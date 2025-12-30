@extends('layouts.main')

@section('content')
    <div class="bg-gray-50 min-h-screen py-12">
        <div class="max-w-screen-xl mx-auto px-4">

            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Riwayat Reservasi</h1>
                <a href="{{ url('/rooms') }}" class="text-yellow-600 font-bold hover:underline text-sm">
                    + Pesan Kamar Baru
                </a>
            </div>

            @if($bookings->isEmpty())
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
                <div class="grid gap-6">
                    @foreach($bookings as $booking)
                        <div
                            class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center hover:shadow-md transition duration-300">
                            <div class="flex gap-6 items-center">
                                <div class="text-center bg-gray-50 p-4 rounded-xl border border-gray-100 min-w-[80px]">
                                    <span class="block text-2xl font-bold text-gray-900">
                                        {{ \Carbon\Carbon::parse($booking->check_in)->format('d') }}
                                    </span>
                                    <span class="block text-xs uppercase text-gray-500 font-bold">
                                        {{ \Carbon\Carbon::parse($booking->check_in)->format('M') }}
                                    </span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span
                                            class="bg-gray-100 text-gray-600 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wide">
                                            {{ $booking->kode_booking }}
                                        </span>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-900">
                                        {{ $booking->kamar ? $booking->kamar->tipe_kamar : 'Tipe Kamar Dihapus' }}
                                    </h3>
                                    <p class="text-gray-500 text-sm mt-1">
                                        <i class="fa-solid fa-moon mr-1"></i> {{ $booking->jumlah_kamar }} Kamar
                                        <span class="mx-2">•</span>
                                        Check-out: {{ \Carbon\Carbon::parse($booking->check_out)->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-4 md:mt-0 flex flex-col items-end gap-2">
                                <span
                                    class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 text-xs font-bold uppercase tracking-wide">
                                    Status: {{ ucfirst($booking->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection