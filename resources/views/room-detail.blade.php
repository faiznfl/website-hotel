@extends('layouts.main')

@section('title', $room->tipe_kamar . ' - Hotel Rumah RB')

@section('content')

    {{-- HEADER GAMBAR BESAR --}}
    <div class="relative h-[60vh] w-full overflow-hidden">
        {{-- Gambar Background --}}
        <img src="{{ asset('storage/' . $room->foto) }}" alt="{{ $room->tipe_kamar }}"
            class="w-full h-full object-cover object-center">

        {{-- Overlay Gelap & Teks --}}
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

                {{-- KOLOM KIRI (Info Kamar - Tidak Berubah) --}}
                <div class="lg:col-span-2 space-y-8">

                    {{-- Deskripsi --}}
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                        <h3 class="text-xl font-bold text-gray-900 mb-4 border-l-4 border-yellow-500 pl-4">
                            DESCRIPTION
                        </h3>
                        <p class="text-gray-600 leading-relaxed text-justify">
                            {{ $room->deskripsi }}
                        </p>
                    </div>

                    {{-- Fasilitas --}}
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 border-l-4 border-yellow-500 pl-4">
                            AMENITIES
                        </h3>
                        @if ($room->fasilitas)
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach (explode(',', $room->fasilitas) as $facility)
                                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                        <div class="text-yellow-500">
                                            <i class="fa-solid fa-check-circle"></i>
                                        </div>
                                        <span class="text-gray-700 text-sm font-medium">{{ trim($facility) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>

                {{-- KOLOM KANAN (Sidebar dengan Pop-up) --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 sticky top-24">

                        {{-- Harga --}}
                        <p class="text-gray-500 text-xs font-bold uppercase mb-1">Price per Night</p>
                        <div class="flex items-baseline gap-1 mb-6 border-b border-gray-100 pb-6">
                            <span class="text-3xl font-bold text-gray-900">
                                Rp {{ number_format($room->harga, 0, ',', '.') }}
                            </span>
                        </div>

                        {{-- AREA POP-UP FORM --}}
                        <div x-data="{ open: false }">

                            {{-- 1. TOMBOL PEMICU (DENGAN LOGIKA LOGIN) --}}
                            
                            @guest
                                {{-- JIKA BELUM LOGIN: Arahkan ke Login --}}
                                <a href="{{ route('login') }}" 
                                   class="w-full block text-center bg-gray-200 text-gray-500 font-bold py-4 rounded-xl uppercase tracking-wider hover:bg-gray-300 transition-all duration-300 shadow-inner mb-4">
                                   <i class="fa-solid fa-lock mr-2"></i> LOGIN TO BOOK
                                </a>
                                <p class="text-[10px] text-center text-gray-400 mb-4">
                                    Anda harus login terlebih dahulu untuk reservasi.
                                </p>
                            @endguest

                            @auth
                                {{-- JIKA SUDAH LOGIN: Boleh Klik Booking --}}
                                <button @click="open = true"
                                    class="w-full bg-gray-900 text-white font-bold py-4 rounded-xl uppercase tracking-wider hover:bg-yellow-600 transition-all duration-300 shadow-lg mb-4 flex justify-center items-center gap-2">
                                    <span>BOOK NOW</span>
                                </button>
                            @endauth


                            {{-- 2. Modal Pop-up --}}
                            <div x-show="open" x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
                                style="display: none;">

                                {{-- Kotak Putih Form --}}
                                <div @click.outside="open = false"
                                    class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden relative">

                                    {{-- Header Modal --}}
                                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                                        <h3 class="text-lg font-bold text-gray-900">Reservasi Kamar</h3>
                                        <button @click="open = false" class="text-gray-400 hover:text-red-500 transition">
                                            <i class="fa-solid fa-times text-xl"></i>
                                        </button>
                                    </div>

                                    {{-- Body Modal (Formulir) --}}
                                    <div class="p-6">
                                        
                                        {{-- Alert Error --}}
                                        @if (session('error'))
                                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 text-sm">
                                                <strong class="font-bold">Error!</strong>
                                                <span class="block sm:inline">{{ session('error') }}</span>
                                            </div>
                                        @endif

                                        <form action="{{ route('booking.store') }}" method="POST" class="space-y-4">
                                            @csrf
                                            <input type="hidden" name="kamar_id" value="{{ $room->id }}">

                                            {{-- Nama (AUTO FILL) --}}
                                            <div>
                                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Lengkap</label>
                                                <input type="text" name="nama_tamu" 
                                                    value="{{ Auth::user()->name ?? '' }}" 
                                                    required placeholder="Nama Anda"
                                                    class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2 text-sm focus:border-yellow-500 outline-none font-medium text-gray-900">
                                            </div>

                                            {{-- No HP --}}
                                            <div>
                                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">WhatsApp</label>
                                                <input type="tel" name="nomor_hp" required placeholder="0812..."
                                                    class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2 text-sm focus:border-yellow-500 outline-none">
                                            </div>

                                            {{-- Tanggal --}}
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Check-In</label>
                                                    <input type="date" name="check_in" required
                                                        class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-yellow-500 outline-none">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Check-Out</label>
                                                    <input type="date" name="check_out" required
                                                        class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-yellow-500 outline-none">
                                                </div>
                                            </div>

                                            {{-- Jumlah --}}
                                            <div>
                                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Jumlah Kamar</label>
                                                <select name="jumlah_kamar"
                                                    class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2 text-sm focus:border-yellow-500 outline-none">
                                                    <option value="1">1 Kamar</option>
                                                    <option value="2">2 Kamar</option>
                                                    <option value="3">3 Kamar</option>
                                                </select>
                                            </div>

                                            {{-- Tombol Submit --}}
                                            <button type="submit"
                                                class="w-full bg-gray-900 text-white font-bold py-3 rounded-xl hover:bg-yellow-600 transition-all shadow-lg mt-2 flex justify-center items-center gap-2">
                                                <span>KIRIM RESERVASI</span>
                                                <i class="fa-solid fa-paper-plane"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- END POP-UP FORM --}}

                        {{-- Tombol WA Manual (Chat Tanya Dulu) --}}
                        <a href="https://wa.me/6285777479609?text=Halo%20Admin,%20saya%20mau%20tanya%20tentang%20{{ urlencode($room->tipe_kamar) }}"
                            target="_blank"
                            class="flex justify-center items-center gap-2 text-green-600 font-bold hover:text-green-700 py-2 border border-green-100 rounded-xl bg-green-50 hover:bg-green-100 transition-colors">
                            <i class="fa-brands fa-whatsapp text-xl"></i> Chat Tanya Dulu
                        </a>

                    </div>
                </div>

            </div>

        </div>
    </section>

@endsection