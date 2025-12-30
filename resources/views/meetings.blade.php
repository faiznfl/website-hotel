@extends('layouts.main')

@section('title', 'Meetings & Events - Hotel Rumah RB')

@section('content')

    <section class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-screen-xl mx-auto px-4">

            {{-- Judul Halaman --}}
            <div class="text-center mb-16">
                <h2 class="text-4xl font-extrabold text-gray-900 tracking-tight uppercase">MEETINGS & EVENTS</h2>
                <div class="w-20 h-1 bg-yellow-400 mx-auto mt-4 rounded-full"></div>
            </div>

            {{-- Grid 2 Kolom --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                {{-- LOOPING DATA DARI DATABASE --}}
                @forelse($meetings as $meeting)
                    <div
                        class="bg-white rounded-xl shadow-lg overflow-hidden group hover:shadow-2xl transition-all duration-300 border border-gray-100 flex flex-col">

                        {{-- Gambar --}}
                        <div class="relative h-72 overflow-hidden">
                            <img src="{{ asset('storage/' . $meeting->gambar) }}" alt="{{ $meeting->judul }}"
                                class="w-full h-full object-cover transform transition duration-700 group-hover:scale-110">

                            {{-- Badge Kapasitas --}}
                            <div
                                class="absolute top-4 right-4 bg-yellow-500 text-white text-xs font-bold px-4 py-2 rounded-full shadow-md uppercase tracking-wide">
                                {{ $meeting->kapasitas }}
                            </div>
                        </div>

                        {{-- Konten --}}
                        <div class="p-8 flex-1 flex flex-col">
                            <h3 class="text-2xl font-bold text-gray-800 mb-3 group-hover:text-yellow-600 transition-colors">
                                {{ $meeting->judul }}
                            </h3>
                            <p class="text-gray-500 text-sm leading-relaxed mb-6 break-words w-full text-justify">
                                {{ $meeting->deskripsi }}
                            </p>

                            {{-- Fasilitas (Looping Tags) --}}
                            @if($meeting->fasilitas)
                                <div class="space-y-3 mb-8">
                                    @foreach (explode(',', $meeting->fasilitas) as $facility)
                                        <div class="flex items-center text-sm text-gray-600">
                                            {{-- Icon Checklist Emas Generic --}}
                                            <i class="fa-solid fa-circle-check w-6 text-yellow-500"></i>
                                            {{ $facility }}
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Tombol --}}
                            <div class="mt-auto">
                                <a href="{{ route('meeting.detail', $meeting->slug) }}"
                                    class="block w-full text-center bg-gray-900 text-white font-bold py-3 rounded-lg hover:bg-yellow-500 hover:text-white transition-all duration-300 shadow-md hover:shadow-lg">
                                    VIEW MORE
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 text-gray-400">
                        <p>Belum ada paket meeting yang tersedia saat ini.</p>
                    </div>
                @endforelse

            </div>

        </div>
    </section>

@endsection