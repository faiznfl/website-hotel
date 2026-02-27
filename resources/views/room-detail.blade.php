@extends('layouts.main')

@section('title', $room->tipe_kamar . ' - Hotel Rumah RB')

@section('content')

    {{-- SIAPKAN DATA GAMBAR UNTUK ALPINE JS LIGHTBOX --}}
    @php
        $galleryImages = $room->galleries ? $room->galleries->map(function ($item) {
            return asset('storage/' . $item->foto);
        })->values() : '[]';
    @endphp

    {{-- HEADER GAMBAR (HERO) --}}
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

                {{-- INFO BAR --}}
                <div class="flex flex-wrap items-center gap-4 md:gap-8 text-white/90 text-xs md:text-base font-medium">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-user-group text-yellow-400 text-sm md:text-lg"></i>
                        <span>{{ $room->max_dewasa }} Dewasa</span>
                    </div>
                    @if($room->max_anak > 0)
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-child text-yellow-400 text-sm md:text-lg"></i>
                            <span>{{ $room->max_anak }} Anak</span>
                        </div>
                    @endif
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-bed text-yellow-400 text-sm md:text-lg"></i>
                        <span>{{ $room->beds }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-bath text-yellow-400 text-sm md:text-lg"></i>
                        <span>{{ $room->baths }} Kamar Mandi</span>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- KONTEN UTAMA (DIBUNGKUS ALPINE JS UNTUK LIGHTBOX) --}}
    <section class="py-8 md:py-12 bg-gray-50 min-h-screen" x-data="{
                    modalOpen: false,
                    images: {{ $galleryImages }},
                    activeImage: '',
                    currentIndex: 0,

                    openGallery(index) {
                        this.currentIndex = index;
                        this.activeImage = this.images[index];
                        this.modalOpen = true;
                        document.body.style.overflow = 'hidden'; 
                    },

                    closeGallery() {
                        this.modalOpen = false;
                        document.body.style.overflow = 'auto'; 
                    },

                    nextImage() {
                        this.currentIndex = (this.currentIndex === this.images.length - 1) ? 0 : this.currentIndex + 1;
                        this.activeImage = this.images[this.currentIndex];
                    },

                    prevImage() {
                        this.currentIndex = (this.currentIndex === 0) ? this.images.length - 1 : this.currentIndex - 1;
                        this.activeImage = this.images[this.currentIndex];
                    }
                }" @keydown.escape.window="closeGallery()" @keydown.arrow-right.window="modalOpen ? nextImage() : null"
        @keydown.arrow-left.window="modalOpen ? prevImage() : null">

        <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-10">

                {{-- KOLOM KIRI (Info Kamar) --}}
                <div class="lg:col-span-2 space-y-6 md:space-y-8">

                    {{-- GALERI FOTO TAMBAHAN (SLIDER + PANAH) --}}
                    @if ($room->galleries && $room->galleries->count() > 0)
                        <div class="bg-white rounded-2xl p-5 md:p-8 shadow-sm border border-gray-100">

                            <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-6 border-l-4 border-yellow-500 pl-4">
                                ROOM GALLERY
                            </h3>

                            {{-- Wrapper Relative + Alpine untuk Scroll Container --}}
                            <div class="relative group" x-data="{
                                        scrollNext() { $refs.sliderContainer.scrollBy({ left: 300, behavior: 'smooth' }); },
                                        scrollPrev() { $refs.sliderContainer.scrollBy({ left: -300, behavior: 'smooth' }); }
                                    }">

                                {{-- Tombol Panah KIRI (Hanya muncul di Desktop saat di-hover) --}}
                                <button @click="scrollPrev()" type="button"
                                    class="hidden md:flex absolute left-2 top-1/2 -translate-y-1/2 z-10 w-10 h-10 items-center justify-center bg-white/90 text-gray-800 rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 hover:bg-yellow-400 hover:text-white">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </button>

                                {{-- CONTAINER SLIDER (Ditambahkan x-ref="sliderContainer") --}}
                                <div x-ref="sliderContainer" class="flex overflow-x-auto gap-4 pb-2 snap-x snap-mandatory"
                                    style="scrollbar-width: none; -ms-overflow-style: none;">
                                    <style>
                                        div::-webkit-scrollbar {
                                            display: none;
                                        }
                                    </style>

                                    @foreach ($room->galleries as $index => $gallery)
                                        <div @click="openGallery({{ $index }})"
                                            class="flex-none w-[85%] sm:w-[45%] lg:w-[48%] relative overflow-hidden rounded-xl bg-gray-200 cursor-pointer shadow-sm aspect-video snap-center transform transition duration-300 hover:-translate-y-1">

                                            <img src="{{ asset('storage/' . $gallery->foto) }}" alt="Foto Kamar"
                                                class="w-full h-full object-cover" loading="lazy">

                                            <div class="absolute inset-0 bg-white/0 hover:bg-white/10 transition duration-300">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Tombol Panah KANAN (Hanya muncul di Desktop saat di-hover) --}}
                                <button @click="scrollNext()" type="button"
                                    class="hidden md:flex absolute right-2 top-1/2 -translate-y-1/2 z-10 w-10 h-10 items-center justify-center bg-white/90 text-gray-800 rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 hover:bg-yellow-400 hover:text-white">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </button>

                            </div>

                            {{-- Petunjuk Geser (Muncul di HP saja) --}}
                            <div class="text-center mt-3 md:hidden">
                                <span class="text-[10px] text-gray-400 flex justify-center items-center gap-2">
                                    <i class="fa-solid fa-arrows-left-right"></i> Geser untuk melihat foto lainnya
                                </span>
                            </div>

                        </div>
                    @endif

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

        {{-- ========================================== --}}
        {{-- MODAL LIGHTBOX --}}
        {{-- ========================================== --}}
        <div x-show="modalOpen" style="display: none;" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/95 backdrop-blur-md p-4">

            <button @click="closeGallery()"
                class="absolute top-4 right-4 md:top-8 md:right-8 text-white/70 hover:text-white hover:rotate-90 transition duration-300 z-50 p-2">
                <i class="fa-solid fa-xmark text-3xl md:text-5xl"></i>
            </button>

            <div class="relative w-full h-full flex items-center justify-center" @click.outside="closeGallery()">

                <button @click.stop="prevImage()"
                    class="absolute left-0 md:left-4 p-3 md:p-5 text-white/70 hover:text-yellow-400 transition transform active:scale-95 z-50">
                    <i class="fa-solid fa-chevron-left text-3xl md:text-5xl drop-shadow-lg"></i>
                </button>

                <img :src="activeImage"
                    class="max-h-[80vh] md:max-h-[90vh] max-w-full md:max-w-[90vw] object-contain rounded-lg shadow-2xl transition-all duration-300 select-none"
                    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-50 scale-95"
                    x-transition:enter-end="opacity-100 scale-100">

                <button @click.stop="nextImage()"
                    class="absolute right-0 md:right-4 p-3 md:p-5 text-white/70 hover:text-yellow-400 transition transform active:scale-95 z-50">
                    <i class="fa-solid fa-chevron-right text-3xl md:text-5xl drop-shadow-lg"></i>
                </button>

            </div>

            <div
                class="absolute bottom-6 md:bottom-8 left-1/2 transform -translate-x-1/2 text-white/80 font-mono text-sm bg-white/10 backdrop-blur px-4 py-1.5 rounded-full border border-white/10">
                <span x-text="currentIndex + 1"></span> / <span x-text="images.length"></span>
            </div>

        </div>

    </section>

@endsection