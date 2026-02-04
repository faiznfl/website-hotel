@extends('layouts.main')

@section('title', 'Gallery - Hotel Rumah RB')

@section('content')

    {{-- SIAPKAN DATA GAMBAR UNTUK ALPINE JS --}}
    @php
        $imageUrls = $galleries->map(function ($item) {
            return asset('storage/' . $item->gambar);
        })->values();
    @endphp

    {{-- WRAPPER UTAMA & ALPINE JS LOGIC --}}
    {{-- padding top (pt) disesuaikan agar tidak terlalu nempel dengan navbar --}}
    <section class="py-12 md:py-20 bg-gray-50 min-h-screen" x-data="{
                    modalOpen: false,
                    images: {{ $imageUrls }},
                    activeImage: '',
                    currentIndex: 0,

                    openGallery(index) {
                        this.currentIndex = index;
                        this.activeImage = this.images[index];
                        this.modalOpen = true;
                        document.body.style.overflow = 'hidden'; // Matikan scroll body
                    },

                    closeGallery() {
                        this.modalOpen = false;
                        document.body.style.overflow = 'auto'; // Hidupkan scroll body
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

        <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- HEADER HALAMAN (Tanpa Hero Image) --}}
            <div class="text-center mb-10 md:mb-16">
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight uppercase">OUR GALLERY</h2>
                <div class="w-16 md:w-20 h-1 bg-yellow-400 mx-auto mt-3 md:mt-4 rounded-full"></div>
                <p class="mt-4 text-gray-500 text-sm md:text-base max-w-2xl mx-auto">
                    Capture moments of comfort and joy at Hotel Rumah RB.
                </p>
            </div>

            {{-- GRID FOTO (Instagram Style) --}}
            {{-- HP: 2 Kolom, Tablet: 3 Kolom, Desktop: 4 Kolom --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-6">

                @forelse($galleries as $index => $gallery)
                    <div @click="openGallery({{ $index }})"
                        class="relative group overflow-hidden rounded-xl shadow-sm hover:shadow-lg cursor-pointer aspect-square bg-gray-200 transition-all duration-300">

                        {{-- GAMBAR --}}
                        <img class="w-full h-full object-cover transform transition duration-700 group-hover:scale-110"
                            src="{{ asset('storage/' . $gallery->gambar) }}" alt="Gallery Image" loading="lazy">

                        {{-- OVERLAY GELAP & ICON (Muncul saat Hover) --}}
                        <div
                            class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <div
                                class="bg-white/20 text-white transform translate-y-4 group-hover:translate-y-0 transition duration-300">
                            </div>
                        </div>

                    </div>
                @empty
                    <div class="col-span-full text-center py-20 bg-white rounded-xl border border-dashed border-gray-300">
                        <div class="text-gray-400 mb-2">
                            <i class="fa-regular fa-images text-4xl"></i>
                        </div>
                        <p class="text-gray-500">Belum ada foto galeri yang diupload.</p>
                    </div>
                @endforelse

            </div>

            {{-- FOOTER LINK INSTAGRAM --}}
            <div class="text-center mt-12 md:mt-16 border-t border-gray-200 pt-8">
                <a href="https://instagram.com" target="_blank"
                    class="inline-flex items-center gap-2 text-gray-500 hover:text-yellow-600 transition text-sm md:text-base font-medium">
                    <i class="fa-brands fa-instagram text-xl"></i>
                    Follow us on Instagram @hotelrumahrb
                </a>
            </div>

        </div>

        {{-- ========================================== --}}
        {{-- MODAL LIGHTBOX (Pop-up Layar Penuh) --}}
        {{-- ========================================== --}}
        <div x-show="modalOpen" style="display: none;" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/95 backdrop-blur-md p-4">

            {{-- Tombol Close --}}
            <button @click="closeGallery()"
                class="absolute top-4 right-4 md:top-8 md:right-8 text-white/70 hover:text-white hover:rotate-90 transition duration-300 z-50 p-2">
                <i class="fa-solid fa-xmark text-3xl md:text-5xl"></i>
            </button>

            {{-- Container Gambar --}}
            <div class="relative w-full h-full flex items-center justify-center" @click.outside="closeGallery()">

                {{-- Prev --}}
                <button @click.stop="prevImage()"
                    class="absolute left-0 md:left-4 p-3 md:p-5 text-white/70 hover:text-yellow-400 transition transform active:scale-95 z-50">
                    <i class="fa-solid fa-chevron-left text-3xl md:text-5xl drop-shadow-lg"></i>
                </button>

                {{-- Gambar Besar --}}
                <img :src="activeImage"
                    class="max-h-[80vh] md:max-h-[90vh] max-w-full md:max-w-[90vw] object-contain rounded-lg shadow-2xl transition-all duration-300 select-none"
                    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-50 scale-95"
                    x-transition:enter-end="opacity-100 scale-100">

                {{-- Next --}}
                <button @click.stop="nextImage()"
                    class="absolute right-0 md:right-4 p-3 md:p-5 text-white/70 hover:text-yellow-400 transition transform active:scale-95 z-50">
                    <i class="fa-solid fa-chevron-right text-3xl md:text-5xl drop-shadow-lg"></i>
                </button>

            </div>

            {{-- Indikator Angka --}}
            <div
                class="absolute bottom-6 md:bottom-8 left-1/2 transform -translate-x-1/2 text-white/80 font-mono text-sm bg-white/10 backdrop-blur px-4 py-1.5 rounded-full border border-white/10">
                <span x-text="currentIndex + 1"></span> / <span x-text="images.length"></span>
            </div>

        </div>

    </section>

@endsection