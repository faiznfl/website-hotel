@extends('layouts.main')

@section('title', 'Rooms & Suites')

@section('content')

    {{-- HERO SECTION --}}
    <div class="relative w-full h-[300px] md:h-[400px] overflow-hidden">
        <img src="{{ asset('img/hotel-luar.png') }}" alt="Hotel Exterior"
            class="absolute inset-0 w-full h-full object-cover">

        {{-- <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
            <h1 class="text-4xl md:text-5xl font-bold text-white tracking-wider shadow-lg">ROOMS & SUITES</h1>
        </div> --}}
    </div>

    {{-- MAIN CONTENT --}}
    <section class="py-16 bg-gray-50">
        <div class="max-w-screen-xl mx-auto px-4">

            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900">ROOMS & SUITE</h2>
                <div class="w-20 h-1 bg-yellow-400 mx-auto mt-4 rounded"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                @foreach($rooms as $kamar)
                    <div
                        class="bg-white border border-gray-200 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 flex flex-col h-full overflow-hidden group">

                        <div class="relative h-64 overflow-hidden">
                            <img src="{{ asset('storage/' . $kamar->foto) }}" alt="{{ $kamar->tipe_kamar }}"
                                class="w-full h-full object-cover transform group-hover:scale-110 transition duration-500">
                        </div>

                        <div class="p-6 flex flex-col flex-grow">

                            <h3 class="text-2xl font-bold text-gray-900 mb-2 group-hover:text-yellow-600 transition-colors">
                                {{ $kamar->tipe_kamar }}
                            </h3>

                            <div class="flex flex-wrap gap-4 text-sm text-gray-500 mb-6 mt-2">
                                <span class="flex items-center gap-1 bg-gray-100 px-2 py-1 rounded">
                                    <i class="fa-solid fa-user text-yellow-600"></i> {{ $kamar->max_dewasa }} Dewasa
                                </span>

                                @if($kamar->max_anak > 0)
                                    <span class="flex items-center gap-1 bg-gray-100 px-2 py-1 rounded">
                                        <i class="fa-solid fa-child text-yellow-600"></i> {{ $kamar->max_anak }} Anak
                                    </span>
                                @endif

                                <span class="flex items-center gap-1 bg-gray-100 px-2 py-1 rounded">
                                    <i class="fa-solid fa-bed text-yellow-600"></i> {{ $kamar->beds }} Bed
                                </span>

                                <span class="flex items-center gap-1 bg-gray-100 px-2 py-1 rounded">
                                    <i class="fa-solid fa-bath text-yellow-600"></i> {{ $kamar->baths }} Bath
                                </span>
                            </div>

                            <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-gray-400">Start from</p>
                                    <p class="text-xl font-bold text-yellow-600">
                                        Rp {{ number_format($kamar->harga, 0, ',', '.') }}
                                        <span class="text-sm text-gray-500 font-normal">/ malam</span>
                                    </p>
                                </div>

                                <a href="{{ route('room.detail', $kamar->slug) }}"
                                    class="text-white bg-yellow-500 hover:bg-yellow-800 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-sm px-4 py-2 transition-colors shadow-lg">
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