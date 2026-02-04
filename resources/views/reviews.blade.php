@extends('layouts.main')

@section('title', 'Semua Review - Hotel Rumah RB')

@section('content')

    {{-- 1. HEADER HALAMAN (Desain Awal: Hitam) --}}
    <div class="bg-gray-900 py-12 md:py-16 text-center text-white">
        <div class="max-w-screen-xl mx-auto px-4">
            <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight mb-4">Kata Mereka</h1>
            <p class="text-gray-400 text-sm md:text-lg max-w-2xl mx-auto leading-relaxed">
                Transparansi adalah prioritas kami. Berikut adalah pengalaman jujur dari para tamu yang telah menginap di
                Hotel Rumah RB.
            </p>

            {{-- Tombol Kembali ke Home --}}
            <a href="{{ route('home') }}"
                class="inline-flex items-center gap-2 mt-6 md:mt-8 text-yellow-400 hover:text-yellow-300 font-semibold transition text-sm md:text-base">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>
    </div>

    {{-- 2. LIST REVIEW (GRID) --}}
    <div class="py-12 md:py-16 bg-gray-50 min-h-screen">
        <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">

            @if($reviews->count() > 0)
                {{-- Grid Responsif: 1 kolom di HP, 2 di Tablet, 3 di Laptop --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8 mb-12">
                    @foreach($reviews as $testi)
                        {{-- Tambah flex & h-full agar tinggi kartu sama rata --}}
                        <div
                            class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-gray-100 relative hover:-translate-y-1 hover:shadow-md transition duration-300 flex flex-col h-full">

                            {{-- Ikon Kutipan --}}
                            <div class="absolute top-4 right-6 text-gray-100 text-5xl md:text-6xl font-serif">”</div>

                            {{-- Bintang --}}
                            <div class="flex text-yellow-400 mb-4 text-sm relative z-10">
                                @for($i = 0; $i < $testi->stars; $i++)
                                    <i class="fa-solid fa-star"></i>
                                @endfor
                            </div>

                            {{-- Isi Review (flex-grow agar footer selalu di bawah) --}}
                            <p class="text-gray-600 italic mb-6 leading-relaxed text-sm relative z-10 flex-grow">
                                "{{ $testi->content }}"
                            </p>

                            {{-- Profil Pengguna --}}
                            <div class="flex items-center gap-4 relative z-10 mt-auto">
                                {{-- Avatar Inisial --}}
                                <div
                                    class="w-10 h-10 flex-shrink-0 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm border border-blue-200 shadow-sm">
                                    {{ substr($testi->name, 0, 1) }}
                                </div>

                                <div>
                                    <h4 class="font-bold text-gray-900 text-sm">{{ $testi->name }}</h4>
                                    <span class="text-xs text-gray-400 block">
                                        {{-- Menampilkan tanggal: "2 hari yang lalu" --}}
                                        {{ $testi->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- 3. PAGINATION (Nomor Halaman) --}}
                <div class="flex justify-center mt-8 md:mt-10">
                    <div class="bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-100">
                        {{ $reviews->links() }}
                    </div>
                </div>

            @else
                {{-- Tampilan Kalau Belum Ada Review --}}
                <div class="text-center py-20">
                    <div class="text-gray-300 text-5xl md:text-6xl mb-4">
                        <i class="fa-regular fa-comment-dots"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Belum ada review</h3>
                    <p class="text-gray-500 mt-2 text-sm md:text-base">Jadilah tamu pertama yang memberikan ulasan!</p>
                    <a href="{{ route('home') }}#testimoni"
                        class="mt-6 inline-block bg-yellow-400 text-white px-6 py-2 rounded-lg font-bold hover:bg-yellow-500 transition shadow-lg text-sm md:text-base">
                        Tulis Review
                    </a>
                </div>
            @endif

        </div>
    </div>

@endsection