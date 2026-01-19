@extends('layouts.main')

@section('title', 'Gallery - Hotel Rumah RB')

@section('content')

    {{-- SIAPKAN DATA GAMBAR AGAR BISA DIBACA JAVASCRIPT --}}
    @php
        $imageUrls = $galleries->map(function($item) {
            return asset('storage/' . $item->gambar);
        })->values();
    @endphp

    {{-- WRAPPER ALPINE JS --}}
    <section class="py-12 bg-gray-50 min-h-screen"
             x-data="{
                modalOpen: false,
                images: {{ $imageUrls }},
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
             }"
             @keydown.escape.window="closeGallery()"
             @keydown.arrow-right.window="modalOpen ? nextImage() : null"
             @keydown.arrow-left.window="modalOpen ? prevImage() : null">

        <div class="max-w-screen-2xl mx-auto px-4">

            <div class="text-center mb-12">
                <h2 class="text-4xl font-extrabold text-gray-900 tracking-tight uppercase">OUR GALLERY</h2>
                <div class="w-20 h-1 bg-yellow-400 mx-auto mt-4 rounded-full"></div>
            </div>

            {{-- Grid Container --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">

                @forelse($galleries as $index => $gallery)
                    <div @click="openGallery({{ $index }})" 
                         class="relative group overflow-hidden rounded-xl shadow-lg aspect-square cursor-pointer">

                        {{-- GAMBAR (Efek Zoom) --}}
                        <img class="w-full h-full object-cover transform transition duration-500 group-hover:scale-110"
                            src="{{ asset('storage/' . $gallery->gambar) }}" 
                            alt="Gallery Image" 
                            loading="lazy">
                        
                        {{-- OVERLAY GELAP SAAT HOVER (TANPA ICON) --}}
                        {{-- bg-black/30 = Hitam transparansi 30%. Ubah angkanya jika kurang/terlalu gelap --}}
                        <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 text-gray-400">
                        <p>Belum ada foto galeri yang diupload.</p>
                    </div>
                @endforelse

            </div>

            <div class="text-center mt-12">
                <p class="text-gray-400 italic text-sm">Follow us on Instagram for more updates @hotelrumahrb</p>
            </div>

        </div>

        {{-- ========================================== --}}
        {{-- MODAL LIGHTBOX (Pop-up Layar Penuh)        --}}
        {{-- ========================================== --}}
        <div x-show="modalOpen" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/95 backdrop-blur-sm p-4">

            {{-- Tombol Close --}}
            <button @click="closeGallery()" class="absolute top-6 right-6 text-white/70 hover:text-white transition z-50">
                <i class="fa-solid fa-xmark text-4xl"></i>
            </button>

            {{-- Container Gambar --}}
            <div class="relative w-full h-full flex items-center justify-center" @click.outside="closeGallery()">
                
                {{-- Prev --}}
                <button @click.stop="prevImage()" 
                        class="absolute left-2 md:left-8 p-4 text-white/70 hover:text-yellow-400 transition transform hover:scale-110 z-50 bg-black/20 rounded-full hover:bg-black/50">
                    <i class="fa-solid fa-chevron-left text-3xl md:text-5xl"></i>
                </button>

                {{-- Gambar Besar --}}
                <img :src="activeImage" 
                     class="max-h-[90vh] max-w-[90vw] object-contain rounded shadow-2xl transition-all duration-300 select-none"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-50 scale-95"
                     x-transition:enter-end="opacity-100 scale-100">

                {{-- Next --}}
                <button @click.stop="nextImage()" 
                        class="absolute right-2 md:right-8 p-4 text-white/70 hover:text-yellow-400 transition transform hover:scale-110 z-50 bg-black/20 rounded-full hover:bg-black/50">
                    <i class="fa-solid fa-chevron-right text-3xl md:text-5xl"></i>
                </button>

            </div>

            {{-- Indikator Angka --}}
            <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 text-white/80 font-mono text-sm bg-black/50 px-4 py-1 rounded-full">
                <span x-text="currentIndex + 1"></span> / <span x-text="images.length"></span>
            </div>

        </div>

    </section>

@endsection