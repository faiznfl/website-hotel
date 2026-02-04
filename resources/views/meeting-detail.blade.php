@extends('layouts.main')

@section('title', $meeting->judul . ' - Hotel Rumah RB')

@section('content')

    {{-- HEADER IMAGE --}}
    {{-- TWEAK: Tinggi di HP disesuaikan (50vh) agar tidak terlalu panjang --}}
    <div class="relative h-[50vh] md:h-[60vh] w-full overflow-hidden">
        <img src="{{ asset('storage/' . $meeting->gambar) }}" alt="{{ $meeting->judul }}"
            class="w-full h-full object-cover object-center">

        <div class="absolute inset-0 bg-black/50"></div>

        {{-- Judul di Kiri Bawah --}}
        {{-- TWEAK: Padding di HP diperkecil (p-6) --}}
        <div class="absolute bottom-0 left-0 w-full p-6 md:p-12 bg-gradient-to-t from-black/90 to-transparent">
            <div class="max-w-screen-xl mx-auto">
                {{-- TWEAK: Font Size di HP (text-2xl) agar tidak tumpuk --}}
                <h1
                    class="text-2xl sm:text-3xl md:text-5xl font-extrabold text-white uppercase tracking-wide mb-2 shadow-sm leading-tight">
                    {{ $meeting->judul }}
                </h1>
            </div>
        </div>
    </div>

    {{-- KONTEN UTAMA --}}
    <section class="py-8 md:py-12 bg-gray-50 min-h-screen">
        <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-10">

                {{-- KOLOM KIRI (Info) --}}
                <div class="lg:col-span-2 space-y-6 md:space-y-8">

                    {{-- Deskripsi --}}
                    <div class="bg-white rounded-2xl p-5 md:p-8 shadow-sm border border-gray-100">
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-4 border-l-4 border-yellow-500 pl-4">
                            ABOUT THIS VENUE
                        </h3>
                        <div class="text-gray-600 text-sm md:text-base leading-relaxed text-justify prose max-w-none">
                            {!! $meeting->deskripsi !!}
                        </div>
                    </div>

                    {{-- Fasilitas --}}
                    <div class="bg-white rounded-2xl p-5 md:p-8 shadow-sm border border-gray-100">
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-6 border-l-4 border-yellow-500 pl-4">
                            INCLUDED FACILITIES
                        </h3>
                        @if ($meeting->fasilitas)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                                @foreach (explode(',', $meeting->fasilitas) as $facility)
                                    <div
                                        class="flex items-center gap-3 p-3 md:p-4 border border-gray-100 rounded-xl bg-gray-50 hover:bg-yellow-50 transition-colors">
                                        <div
                                            class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-yellow-500 shadow-sm flex-shrink-0">
                                            <i class="fa-solid fa-check"></i>
                                        </div>
                                        <span class="text-gray-700 text-sm md:text-base font-medium">{{ trim($facility) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-400 italic text-sm">Hubungi kami untuk detail fasilitas.</p>
                        @endif
                    </div>

                </div>

                {{-- KOLOM KANAN (Sidebar Info & Contact) --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 sticky top-20 md:top-24">

                        {{-- Info Kapasitas --}}
                        <div class="mb-6 md:mb-8">
                            <h4 class="text-gray-900 font-bold uppercase tracking-wider text-xs md:text-sm mb-3 md:mb-4">
                                Venue Capacity</h4>
                            <div class="flex items-center gap-4 bg-blue-50 p-4 rounded-xl border border-blue-100">
                                <div class="p-3 bg-white rounded-lg text-blue-600">
                                    <i class="fa-solid fa-users text-2xl"></i>
                                </div>
                                <div>
                                    <p class="text-xs md:text-sm text-gray-500">Up to</p>
                                    <p class="text-lg md:text-xl font-bold text-gray-900">{{ $meeting->kapasitas }} Pax</p>
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="space-y-3">
                            <p class="text-xs md:text-sm text-gray-500 text-center mb-2">Interested in this room?</p>

                            {{-- WA Button --}}
                            <a href="https://wa.me/6281363374155?text=Halo%20Admin,%20saya%20ingin%20info%20paket%20meeting%20di%20ruangan%20{{ urlencode($meeting->judul) }}"
                                target="_blank"
                                class="flex justify-center items-center gap-2 w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3.5 md:py-4 rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1 text-sm md:text-base">
                                <i class="fa-brands fa-whatsapp text-lg md:text-xl"></i> Book via WhatsApp
                            </a>

                            {{-- Contact Form Link --}}
                            <a href="/contact"
                                class="block w-full text-center py-3.5 md:py-4 rounded-xl border-2 border-gray-200 text-gray-700 font-bold hover:border-gray-900 hover:text-gray-900 transition-colors text-sm md:text-base">
                                Contact Form
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection