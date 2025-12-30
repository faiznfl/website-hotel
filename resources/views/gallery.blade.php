@extends('layouts.main')

@section('title', 'Gallery - Hotel Rumah RB')

@section('content')

    <section class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-screen-2xl mx-auto px-4">

            <div class="text-center mb-12">
                <h2 class="text-4xl font-extrabold text-gray-900 tracking-tight uppercase">OUR GALLERY</h2>
                <div class="w-20 h-1 bg-yellow-400 mx-auto mt-4 rounded-full"></div>
            </div>

            {{-- Grid Container --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">

                {{-- MULAI LOOPING DATABASE --}}
                @forelse($galleries as $gallery)
                    <div class="relative group overflow-hidden rounded-xl shadow-lg aspect-square">

                        {{-- Menampilkan Gambar dari Storage --}}
                        <img class="w-full h-full object-cover transform transition duration-500 group-hover:scale-110"
                            src="{{ asset('storage/' . $gallery->gambar) }}" alt="Gallery Image" loading="lazy">

                        {{-- Overlay Efek Gelap --}}
                        <div
                            class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            {{-- Karena kita hapus judul/kategori, overlay ini hanya efek gelap saja --}}
                        </div>
                    </div>
                @empty
                    {{-- Tampilan jika belum ada foto sama sekali --}}
                    <div class="col-span-full text-center py-12 text-gray-400">
                        <p>Belum ada foto galeri yang diupload.</p>
                    </div>
                @endforelse
                {{-- SELESAI LOOPING --}}

            </div>

            <div class="text-center mt-12">
                <p class="text-gray-400 italic text-sm">Follow us on Instagram for more updates @hotelrumahrb</p>
            </div>

        </div>
    </section>

@endsection