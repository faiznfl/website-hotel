@extends('layouts.main')

@section('title', $meeting->judul . ' - Hotel Rumah RB')

@section('content')

    {{-- HEADER IMAGE --}}
    <div class="relative h-[60vh] w-full overflow-hidden">
        <img src="{{ asset('storage/' . $meeting->gambar) }}" alt="{{ $meeting->judul }}"
            class="w-full h-full object-cover object-center">

        <div class="absolute inset-0 bg-black/50"></div>
        <div class="absolute bottom-0 left-0 w-full p-8 md:p-12 bg-gradient-to-t from-black/90 to-transparent">
            <div class="max-w-screen-xl mx-auto">
                <span
                    class="inline-block py-1 px-3 rounded-full bg-yellow-500 text-white text-xs font-bold tracking-wider mb-3">
                    EVENT SPACE
                </span>
                <h1 class="text-4xl md:text-5xl font-extrabold text-white uppercase tracking-wide mb-2 shadow-sm">
                    {{ $meeting->judul }}
                </h1>
            </div>
        </div>
    </div>

    {{-- KONTEN --}}
    <section class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-screen-xl mx-auto px-4">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

                {{-- KOLOM KIRI (Info) --}}
                <div class="lg:col-span-2 space-y-8">

                    {{-- Deskripsi --}}
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                        <h3 class="text-xl font-bold text-gray-900 mb-4 border-l-4 border-yellow-500 pl-4">
                            ABOUT THIS VENUE
                        </h3>
                        <p class="text-gray-600 leading-relaxed text-justify">
                            {{ $meeting->deskripsi }}
                        </p>
                    </div>

                    {{-- Fasilitas --}}
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 border-l-4 border-yellow-500 pl-4">
                            INCLUDED FACILITIES
                        </h3>
                        @if ($meeting->fasilitas)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach (explode(',', $meeting->fasilitas) as $facility)
                                    <div
                                        class="flex items-center gap-3 p-4 border border-gray-100 rounded-xl bg-gray-50 hover:bg-yellow-50 transition-colors">
                                        <div
                                            class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-yellow-500 shadow-sm">
                                            <i class="fa-solid fa-check"></i>
                                        </div>
                                        <span class="text-gray-700 font-medium">{{ trim($facility) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-400 italic">Hubungi kami untuk detail fasilitas.</p>
                        @endif
                    </div>

                </div>

                {{-- KOLOM KANAN (Sidebar Info & Contact) --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 sticky top-24">

                        {{-- Info Kapasitas --}}
                        <div class="mb-8">
                            <h4 class="text-gray-900 font-bold uppercase tracking-wider text-sm mb-4">Venue Capacity</h4>
                            <div class="flex items-center gap-4 bg-blue-50 p-4 rounded-xl border border-blue-100">
                                <div class="p-3 bg-white rounded-lg text-blue-600">
                                    <i class="fa-solid fa-users text-2xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Up to</p>
                                    <p class="text-xl font-bold text-gray-900">{{ $meeting->kapasitas }} Pax</p>
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="space-y-3">
                            <p class="text-sm text-gray-500 text-center mb-2">Interested in this room?</p>

                            {{-- WA Button --}}
                            <a href="https://wa.me/6285777479609?text=Halo%20Admin,%20saya%20ingin%20info%20paket%20meeting%20di%20ruangan%20{{ urlencode($meeting->judul) }}"
                                target="_blank"
                                class="flex justify-center items-center gap-2 w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                <i class="fa-brands fa-whatsapp text-xl"></i> Book via WhatsApp
                            </a>

                            {{-- Contact Form Link --}}
                            <a href="/contact"
                                class="block w-full text-center py-4 rounded-xl border-2 border-gray-200 text-gray-700 font-bold hover:border-gray-900 hover:text-gray-900 transition-colors">
                                Contact Form
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection