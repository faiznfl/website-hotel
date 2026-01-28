@extends('layouts.main')

@section('title', $room->tipe_kamar . ' - Hotel Rumah RB')

@section('content')

    {{-- HEADER GAMBAR --}}
    <div class="relative h-[60vh] w-full overflow-hidden">
        <img src="{{ asset('storage/' . $room->foto) }}" alt="{{ $room->tipe_kamar }}"
            class="w-full h-full object-cover object-center">
        <div class="absolute inset-0 bg-black/40"></div>
        <div class="absolute bottom-0 left-0 w-full p-8 md:p-12 bg-gradient-to-t from-black/90 to-transparent">
            <div class="max-w-screen-xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-extrabold text-white uppercase tracking-wide mb-2 shadow-sm">
                    {{ $room->tipe_kamar }}
                </h1>
                <p class="text-yellow-400 text-lg font-medium flex items-center gap-2">
                    <i class="fa-solid fa-star"></i> Recommended Room
                </p>
            </div>
        </div>
    </div>

    {{-- KONTEN UTAMA --}}
    <section class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-screen-xl mx-auto px-4">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

                {{-- KOLOM KIRI (Info Kamar) --}}
                <div class="lg:col-span-2 space-y-8">

                    {{-- Deskripsi --}}
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                        <h3 class="text-xl font-bold text-gray-900 mb-4 border-l-4 border-yellow-500 pl-4">DESCRIPTION</h3>
                        <div class="text-gray-600 leading-relaxed text-justify prose max-w-none">
                            {!! $room->deskripsi !!}
                        </div>
                    </div>

                    {{-- Fasilitas --}}
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 border-l-4 border-yellow-500 pl-4">AMENITIES</h3>
                        @if ($room->fasilitas)
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach (explode(',', $room->fasilitas) as $facility)
                                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-100 hover:border-yellow-200 transition">
                                        <div class="text-yellow-500"><i class="fa-solid fa-check-circle"></i></div>
                                        <span class="text-gray-700 text-sm font-medium">{{ trim($facility) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- KOLOM KANAN (Sidebar Harga & Tombol) --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 sticky top-24">

                        <p class="text-gray-500 text-xs font-bold uppercase mb-1">Price per Night</p>
                        <div class="flex items-baseline gap-1 mb-6 border-b border-gray-100 pb-6">
                            <span class="text-3xl font-bold text-gray-900">
                                Rp {{ number_format($room->harga, 0, ',', '.') }}
                            </span>
                        </div>

                        {{-- TOMBOL AKSI --}}
                        <div class="space-y-4">

                            @guest
                                {{-- Jika Belum Login --}}
                                <a href="{{ route('login') }}"
                                    class="w-full block text-center bg-gray-200 text-gray-500 font-bold py-4 rounded-md uppercase tracking-wider hover:bg-gray-300 transition-all duration-300 shadow-inner">
                                    <i class="fa-solid fa-lock mr-2"></i> LOGIN TO BOOK
                                </a>
                                <p class="text-[10px] text-center text-gray-400">Anda harus login terlebih dahulu untuk memesan.</p>
                            @endguest

                            @auth
                                {{-- Jika Sudah Login -> ARAHKAN KE HALAMAN BOOKING --}}
                                <a href="{{ route('booking.create', ['room_id' => $room->id]) }}"
                                    class="w-full block text-center bg-gray-900 text-white font-bold py-4 rounded-md uppercase tracking-wider hover:bg-yellow-600 transition-all duration-300 shadow-lg transform hover:-translate-y-1">
                                    BOOK NOW <i class="fa-solid fa-arrow-right ml-2"></i>
                                </a>
                            @endauth

                            {{-- Tombol WhatsApp (Tanya Dulu) --}}
                            <a href="https://wa.me/6285777479609?text=Halo%20Admin,%20saya%20mau%20tanya%20tentang%20{{ urlencode($room->tipe_kamar) }}"
                                target="_blank"
                                class="w-full block text-center text-green-600 font-bold hover:text-green-700 py-3 border border-green-200 rounded-md bg-green-50 hover:bg-green-100 transition-colors text-sm">
                                <i class="fa-brands fa-whatsapp text-lg mr-1"></i> Chat Tanya Dulu
                            </a>

                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection