@extends('layouts.main')

@section('title', 'Meetings & Events - Hotel Rumah RB')

@section('content')

    {{-- HERO SECTION (Mini) --}}
    <div class="relative w-full h-[250px] sm:h-[300px] md:h-[400px] overflow-hidden">
        <img src="{{ asset('img/hotel-luar.png') }}" alt="Meetings & Events Hero"
            class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/40"></div>
    </div>

    {{-- MAIN CONTENT --}}
    <section class="py-10 md:py-16 bg-gray-50 min-h-screen">
        <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Judul Halaman --}}
            <div class="text-center mb-10 md:mb-16">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight uppercase">MEETINGS
                    & EVENTS</h2>
                <div class="w-16 md:w-20 h-1 bg-yellow-400 mx-auto mt-3 md:mt-4 rounded-full"></div>
                <p class="mt-4 text-gray-500 max-w-2xl mx-auto text-sm md:text-base">
                    Ruang pertemuan modern dan layanan profesional untuk kesuksesan acara Anda.
                </p>
            </div>

            {{-- GRID 2 KOLOM (1 di HP, 2 di Tablet/Laptop) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12">

                {{-- LOOPING DATA --}}
                @forelse($meetings as $meeting)
                    <div
                        class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 flex flex-col h-full group overflow-hidden">

                        {{-- Gambar (Dibuat lebih tinggi: h-72 agar proporsional di 2 kolom) --}}
                        <div class="relative h-56 md:h-72 overflow-hidden">
                            <img src="{{ asset('storage/' . $meeting->gambar) }}" alt="{{ $meeting->judul }}"
                                class="w-full h-full object-cover transform transition duration-700 group-hover:scale-110">

                            {{-- Overlay di HP --}}
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 sm:hidden">
                            </div>
                        </div>

                        {{-- Konten --}}
                        <div class="p-6 md:p-8 flex-1 flex flex-col">

                            {{-- Judul Paket --}}
                            <h3
                                class="text-xl md:text-2xl font-bold text-gray-900 mb-3 group-hover:text-yellow-600 transition-colors">
                                {{ $meeting->judul }}
                            </h3>

                            {{-- Deskripsi --}}
                            <div
                                class="text-gray-500 text-sm leading-relaxed mb-6 line-clamp-3 md:line-clamp-4 prose prose-sm max-w-none">
                                {!! strip_tags($meeting->deskripsi) !!}
                            </div>

                            {{-- Fasilitas (Tags) --}}
                            @if($meeting->fasilitas)
                                <div class="flex flex-wrap gap-2 mb-8">
                                    @foreach (explode(',', $meeting->fasilitas) as $index => $facility)
                                        {{-- Tampilkan lebih banyak fasilitas (max 5) karena kartu lebar --}}
                                        @if($index < 5)
                                            <span
                                                class="inline-flex items-center gap-1.5 bg-gray-50 text-gray-700 text-xs px-3 py-1.5 rounded-full border border-gray-200">
                                                <i class="fa-solid fa-circle-check text-yellow-500"></i>
                                                {{ trim($facility) }}
                                            </span>
                                        @endif
                                    @endforeach
                                    @if(count(explode(',', $meeting->fasilitas)) > 5)
                                        <span class="text-xs text-gray-400 self-center font-medium">+Lainnya</span>
                                    @endif
                                </div>
                            @endif

                            {{-- Tombol --}}
                            <div class="mt-auto pt-6 border-t border-gray-100">
                                <a href="{{ route('meeting.detail', $meeting->slug) }}"
                                    class="block w-full text-center bg-gray-900 text-white font-bold py-3 md:py-4 rounded-lg hover:bg-yellow-500 hover:text-white transition-all duration-300 shadow-md hover:shadow-lg uppercase tracking-wide text-sm md:text-base">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-20 bg-white rounded-xl border border-dashed border-gray-300">
                        <div class="text-gray-400 mb-2">
                            <i class="fa-regular fa-calendar-xmark text-4xl"></i>
                        </div>
                        <p class="text-gray-500">Belum ada paket meeting yang tersedia saat ini.</p>
                    </div>
                @endforelse

            </div>

        </div>
    </section>

@endsection