@extends('layouts.main')

@section('title', 'Rooms & Suites')

@section('content')

    {{-- HERO SECTION --}}
    {{-- Tinggi Hero: HP (250px), Tablet (300px), Desktop (400px) --}}
    <div class="relative w-full h-[250px] sm:h-[300px] md:h-[400px] overflow-hidden">
        <img src="{{ asset('img/hotel-luar.png') }}" alt="Hotel Exterior"
            class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/40"></div>
    </div>

    {{-- MAIN CONTENT --}}
    {{-- Padding Section: py-10 (HP), py-16 (Desktop) --}}
    <section class="py-10 md:py-16 bg-gray-50">
        <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-8 md:mb-12">
                {{-- Judul Responsif --}}
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900">ROOMS & SUITE</h2>
                <div class="w-16 md:w-20 h-1 bg-yellow-400 mx-auto mt-3 md:mt-4 rounded"></div>
            </div>

            {{-- GRID LAYOUT: 1 Kolom (HP), 2 Kolom (Tablet), 3 Kolom (Laptop) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">

                @foreach($rooms as $kamar)
                    <div
                        class="bg-white border border-gray-200 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 flex flex-col h-full overflow-hidden group">

                        {{-- Tinggi Gambar Kartu: h-48 (HP), h-56 (Tablet), h-64 (Desktop) --}}
                        <div class="relative h-48 sm:h-56 md:h-64 overflow-hidden">
                            <img src="{{ asset('storage/' . $kamar->foto) }}" alt="{{ $kamar->tipe_kamar }}"
                                class="w-full h-full object-cover transform group-hover:scale-110 transition duration-500">
                        </div>

                        <div class="p-4 sm:p-5 md:p-6 flex flex-col flex-grow">

                            {{-- Judul Kamar --}}
                            <h3
                                class="text-lg sm:text-xl md:text-2xl font-bold text-gray-900 mb-2 group-hover:text-yellow-600 transition-colors line-clamp-1">
                                {{ $kamar->tipe_kamar }}
                            </h3>

                            {{-- Fasilitas (Tags) --}}
                            <div class="flex flex-wrap gap-2 md:gap-4 text-xs sm:text-sm text-gray-500 mb-4 md:mb-6 mt-2">
                                <span class="flex items-center gap-1 bg-gray-100 px-2 py-1 rounded whitespace-nowrap">
                                    <i class="fa-solid fa-user text-yellow-600"></i> {{ $kamar->max_dewasa }} Dewasa
                                </span>

                                @if($kamar->max_anak > 0)
                                    <span class="flex items-center gap-1 bg-gray-100 px-2 py-1 rounded whitespace-nowrap">
                                        <i class="fa-solid fa-child text-yellow-600"></i> {{ $kamar->max_anak }} Anak
                                    </span>
                                @endif

                                <span class="flex items-center gap-1 bg-gray-100 px-2 py-1 rounded whitespace-nowrap">
                                    <i class="fa-solid fa-bed text-yellow-600"></i> {{ $kamar->beds }}
                                </span>

                                <span class="flex items-center gap-1 bg-gray-100 px-2 py-1 rounded whitespace-nowrap">
                                    <i class="fa-solid fa-bath text-yellow-600"></i> {{ $kamar->baths }} Bath
                                </span>
                            </div>

                            {{-- Footer Kartu (Harga & Tombol) --}}
                            <div
                                class="mt-auto pt-3 md:pt-4 border-t border-gray-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-0">
                                <div>
                                    <p class="text-[10px] sm:text-xs text-gray-400">Mulai dari</p>
                                    <p class="text-lg sm:text-xl font-bold text-yellow-600">
                                        Rp {{ number_format($kamar->harga, 0, ',', '.') }}
                                        <span class="text-xs sm:text-sm text-gray-500 font-normal">/ malam</span>
                                    </p>
                                </div>

                                {{-- Tombol Full Width di HP, Normal di Tablet ke atas --}}
                                <a href="{{ route('room.detail', $kamar->slug) }}"
                                    class="w-full sm:w-auto text-center text-white bg-yellow-500 hover:bg-yellow-800 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-xs sm:text-sm px-4 py-2.5 sm:py-2 transition-colors shadow-md hover:shadow-lg">
                                    View Details
                                </a>
                            </div>

                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </section>

@endsection