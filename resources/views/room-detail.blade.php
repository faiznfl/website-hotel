@extends('layouts.main')

@section('title', $room->tipe_kamar . ' - Hotel Rumah RB')

@section('content')

    {{-- HEADER GAMBAR --}}
    <div class="relative h-[50vh] md:h-[60vh] w-full overflow-hidden">
        <img src="{{ asset('storage/' . $room->foto) }}" alt="{{ $room->tipe_kamar }}"
            class="w-full h-full object-cover object-center">

        {{-- Overlay Gelap --}}
        <div class="absolute inset-0 bg-black/40"></div>

        {{-- Judul & Info di Kiri Bawah --}}
        <div class="absolute bottom-0 left-0 w-full p-6 md:p-12 bg-gradient-to-t from-black/90 via-black/50 to-transparent">
            <div class="max-w-screen-xl mx-auto">

                {{-- JUDUL KAMAR --}}
                <h1
                    class="text-2xl sm:text-3xl md:text-5xl font-extrabold text-white uppercase tracking-wide shadow-sm leading-tight mb-3 md:mb-4">
                    {{ $room->tipe_kamar }}
                </h1>

                {{--
                === INFO BAR DI BAWAH JUDUL ===
                Menggunakan Flexbox agar berjejer rapi.
                Teks putih, Ikon kuning.
                --}}
                <div class="flex flex-wrap items-center gap-4 md:gap-8 text-white/90 text-xs md:text-base font-medium">

                    {{-- Dewasa --}}
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-user-group text-yellow-400 text-sm md:text-lg"></i>
                        <span>{{ $room->max_dewasa }} Dewasa</span>
                    </div>

                    {{-- Anak (Kondisional) --}}
                    @if($room->max_anak > 0)
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-child text-yellow-400 text-sm md:text-lg"></i>
                            <span>{{ $room->max_anak }} Anak</span>
                        </div>
                    @endif

                    {{-- Bed --}}
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-bed text-yellow-400 text-sm md:text-lg"></i>
                        <span>{{ $room->beds }}</span>
                    </div>

                    {{-- Bath --}}
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-bath text-yellow-400 text-sm md:text-lg"></i>
                        <span>{{ $room->baths }} Kamar Mandi</span>
                    </div>

                </div>

            </div>
        </div>
    </div>

    {{-- KONTEN UTAMA --}}
    <section class="py-8 md:py-12 bg-gray-50 min-h-screen">
        <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-10">

                {{-- KOLOM KIRI (Info Kamar) --}}
                <div class="lg:col-span-2 space-y-6 md:space-y-8">

                    {{-- Deskripsi --}}
                    <div class="bg-white rounded-2xl p-5 md:p-8 shadow-sm border border-gray-100">
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-4 border-l-4 border-yellow-500 pl-4">
                            DESCRIPTION</h3>
                        <div class="text-gray-600 text-sm md:text-base leading-relaxed text-justify prose max-w-none">
                            {!! $room->deskripsi !!}
                        </div>
                    </div>

                    {{-- Fasilitas --}}
                    <div class="bg-white rounded-2xl p-5 md:p-8 shadow-sm border border-gray-100">
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-6 border-l-4 border-yellow-500 pl-4">
                            AMENITIES</h3>
                        @if ($room->fasilitas)
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-4">
                                @foreach (explode(',', $room->fasilitas) as $facility)
                                    <div
                                        class="flex items-center gap-2.5 p-2.5 md:p-3 bg-gray-50 rounded-lg border border-gray-100 hover:border-yellow-200 transition">
                                        <div class="text-yellow-500 text-sm md:text-base"><i class="fa-solid fa-circle-check"></i>
                                        </div>
                                        <span class="text-gray-700 text-xs md:text-sm font-medium">{{ trim($facility) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- KOLOM KANAN (Sidebar Harga & Tombol) --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 sticky top-20 md:top-24">

                        <p class="text-gray-500 text-[10px] md:text-xs font-bold uppercase mb-1">Harga per Malam</p>
                        <div class="flex items-baseline gap-1 mb-6 border-b border-gray-100 pb-6">
                            <span class="text-2xl md:text-3xl font-bold text-gray-900">
                                Rp {{ number_format($room->harga, 0, ',', '.') }}
                            </span>
                        </div>

                        {{-- TOMBOL AKSI --}}
                        <div class="space-y-3 md:space-y-4">

                            @guest
                                <a href="{{ route('login') }}"
                                    class="w-full block text-center bg-gray-900 text-white font-bold py-3.5 md:py-4 rounded-md uppercase tracking-wider hover:bg-yellow-600 transition-all duration-300 shadow-lg transform hover:-translate-y-1 text-sm md:text-base">
                                    Login to Book
                                </a>
                            @endguest

                            @auth
                                <a href="{{ route('booking.create', ['room_id' => $room->id]) }}"
                                    class="w-full block text-center bg-gray-900 text-white font-bold py-3.5 md:py-4 rounded-md uppercase tracking-wider hover:bg-yellow-600 transition-all duration-300 shadow-lg transform hover:-translate-y-1 group text-sm md:text-base">
                                    BOOK NOW <i
                                        class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                                </a>
                            @endauth

                            <a href="https://wa.me/6281363374155?text=Halo%20Admin,%20saya%20mau%20tanya%20tentang%20{{ urlencode($room->tipe_kamar) }}"
                                target="_blank"
                                class="w-full block text-center text-green-600 font-bold hover:text-green-700 py-2.5 md:py-3 border border-green-200 rounded-md bg-green-50 hover:bg-green-100 transition-colors text-xs md:text-sm">
                                <i class="fa-brands fa-whatsapp text-base mr-1"></i> Chat Tanya Dulu
                            </a>

                        </div>

                        <div class="mt-6 text-center">
                            <p class="text-[10px] text-gray-400 flex justify-center items-center gap-1">
                                <i class="fa-solid fa-shield-halved"></i> Best Price Guarantee
                            </p>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection