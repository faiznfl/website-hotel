@extends('layouts.main')

@section('title', 'Restoran - Hotel Rumah RB')

@section('content')

    {{-- 1. HEADER IMAGE (Banner Standar) --}}
    <div class="relative h-[300px] md:h-[400px] w-full bg-gray-900">
        <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?q=80&w=2070&auto=format&fit=crop"
            alt="Restoran" class="w-full h-full object-cover opacity-60">
        <div class="absolute inset-0 flex flex-col justify-center items-center text-center px-4">
            <h1 class="text-3xl md:text-5xl font-bold text-white tracking-tight mb-2">Restoran & Dining</h1>
            <div class="w-16 h-1 bg-yellow-500 rounded mb-4"></div>
            <p class="text-gray-200 max-w-xl text-sm md:text-base">
                Menyajikan hidangan terbaik dengan cita rasa lokal dan internasional.
            </p>
        </div>
    </div>

    {{-- 2. KONTEN UTAMA --}}
    <div class="py-12 bg-white">
        <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

                {{-- KOLOM KIRI: DAFTAR MENU --}}
                <div class="lg:col-span-2">

                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Daftar Menu</h2>
                    </div>

                    @if($menus->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            @foreach($menus as $menu)
                                <div
                                    class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition">
                                    {{-- Gambar Menu --}}
                                    <div class="h-48 bg-gray-100 relative">
                                        @if($menu->image)
                                            <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}"
                                                class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <i class="fa-solid fa-utensils text-3xl"></i>
                                            </div>
                                        @endif

                                        {{-- Badge Kategori --}}
                                        <div class="absolute top-3 left-3">
                                            <span
                                                class="bg-white text-gray-800 text-xs font-bold px-3 py-1 rounded-full shadow-sm uppercase tracking-wide border border-gray-100">
                                                {{ $menu->category }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Info Menu --}}
                                    <div class="p-5">
                                        <div class="flex justify-between items-start mb-2">
                                            <h3 class="font-bold text-gray-900 text-lg">{{ $menu->name }}</h3>
                                            <span class="text-yellow-600 font-bold">
                                                {{ number_format($menu->price / 1000, 0) }}K
                                            </span>
                                        </div>
                                        <p class="text-gray-500 text-sm leading-relaxed mb-4 line-clamp-2">
                                            {{ $menu->description }}
                                        </p>

                                        {{-- Tombol Pesan (Simpel) --}}
                                        <a href="https://wa.me/6281363374155?text=Saya%20ingin%20pesan%20menu:%20{{ $menu->name }}"
                                            target="_blank"
                                            class="block w-full text-center py-2 rounded-lg border border-gray-300 text-gray-700 font-semibold text-sm hover:bg-gray-900 hover:text-white hover:border-gray-900 transition-colors">
                                            Pesan
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- State Kosong --}}
                        <div class="bg-gray-50 rounded-xl p-10 text-center border border-dashed border-gray-300">
                            <i class="fa-solid fa-utensils text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-500">Menu belum tersedia saat ini.</p>
                        </div>
                    @endif

                </div>

                {{-- KOLOM KANAN: SIDEBAR INFO (Sticky) --}}
                <div class="lg:col-span-1">
                    <div class="sticky top-24 space-y-6">

                        {{-- Card Info --}}
                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-6">
                            <h3 class="font-bold text-gray-900 text-lg mb-4 pb-2 border-b border-gray-200">
                                Jam Operasional
                            </h3>
                            <ul class="space-y-4 text-sm">
                                <li class="flex justify-between">
                                    <span class="text-gray-600">Sarapan</span>
                                    <span class="font-semibold text-gray-900">06:00 - 10:00</span>
                                </li>
                                <li class="flex justify-between">
                                    <span class="text-gray-600">Makan Siang</span>
                                    <span class="font-semibold text-gray-900">11:00 - 15:00</span>
                                </li>
                                <li class="flex justify-between">
                                    <span class="text-gray-600">Makan Malam</span>
                                    <span class="font-semibold text-gray-900">18:00 - 22:00</span>
                                </li>
                                <li class="flex justify-between">
                                    <span class="text-gray-600">Room Service</span>
                                    <span class="font-semibold text-gray-900">24 Jam</span>
                                </li>
                            </ul>
                        </div>

                        {{-- Card Kontak --}}
                        <div class="bg-white border border-yellow-200 rounded-xl p-6 shadow-sm">
                            <h3 class="font-bold text-gray-900 text-lg mb-2">Reservasi Meja</h3>
                            <p class="text-gray-500 text-sm mb-4">
                                Hubungi kami untuk pemesanan tempat atau acara khusus.
                            </p>
                            <a href="https://wa.me/6281363374155?text=Halo%20Admin,%20saya%20mau%20reservasi%20meja"
                                target="_blank"
                                class="flex items-center justify-center gap-2 w-full bg-yellow-500 hover:bg-yellow-400 text-black font-bold py-3 rounded-lg transition text-sm">
                                <i class="fa-brands fa-whatsapp"></i> Hubungi via WhatsApp
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection