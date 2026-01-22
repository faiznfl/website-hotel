@extends('layouts.main')

@section('title', 'Home - Hotel Rumah RB')

@section('content')

    {{-- 1. HERO SECTION --}}
    <section class="relative w-full h-[500px] md:h-[600px] overflow-hidden">
        <img src="{{ asset('img/hotel-luar.png') }}" alt="Hotel Rumah RB Exterior"
            class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/30"></div>
    </section>

    {{-- 2. EXPERIENCE SECTION --}}
    <section class="py-16 bg-white">
        <div class="max-w-screen-xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Rumah RB Experience</h2>
                <div class="w-24 h-1 bg-yellow-400 mx-auto mb-6"></div>
                <p class="text-gray-500 max-w-3xl mx-auto text-lg">
                    Discover the traditional Padang escape at Hotel Rumah RB...
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                {{-- Icon 1 --}}
                <div class="text-center p-6 bg-gray-50 rounded-lg hover:shadow-lg transition-shadow duration-300">
                    <div
                        class="w-16 h-16 mx-auto bg-blue-100 rounded-full flex items-center justify-center mb-4 text-yellow-400 text-2xl">
                        <i class="fa-solid fa-square-parking"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-2 text-gray-900">On-site Parking</h4>
                    <p class="text-gray-600">Enjoy the comfort of our spacious and secure on-site parking area.</p>
                </div>
                {{-- Icon 2 --}}
                <div class="text-center p-6 bg-gray-50 rounded-lg hover:shadow-lg transition-shadow duration-300">
                    <div
                        class="w-16 h-16 mx-auto bg-blue-100 rounded-full flex items-center justify-center mb-4 text-yellow-400 text-2xl">
                        <i class="fa-solid fa-wifi"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-2 text-gray-900">Wi-Fi</h4>
                    <p class="text-gray-600">Enjoy fast, free Wi-Fi in all rooms and common areas.</p>
                </div>
                {{-- Icon 3 --}}
                <div class="text-center p-6 bg-gray-50 rounded-lg hover:shadow-lg transition-shadow duration-300">
                    <div
                        class="w-16 h-16 mx-auto bg-blue-100 rounded-full flex items-center justify-center mb-4 text-yellow-400 text-2xl">
                        <i class="fa-solid fa-car-side"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-2 text-gray-900">Car Wash</h4>
                    <p class="text-gray-600">Car wash service is available exclusively for guests bringing their own
                        vehicles.</p>
                </div>
                {{-- Icon 4 --}}
                <div class="text-center p-6 bg-gray-50 rounded-lg hover:shadow-lg transition-shadow duration-300">
                    <div
                        class="w-16 h-16 mx-auto bg-blue-100 rounded-full flex items-center justify-center mb-4 text-yellow-400 text-2xl">
                        <i class="fa-solid fa-utensils"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-2 text-gray-900">Restaurant</h4>
                    <p class="text-gray-600">Enjoy a delightful dining experience at our on-site restaurant.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- 3. ROOM & SUITES SECTION --}}
    <section class="py-16 bg-gray-50">
        <div class="max-w-screen-xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div>
                    <h3 class="text-3xl font-bold text-gray-900 mb-4">ROOM & SUITES</h3>
                    <p class="text-gray-600 mb-6 text-lg leading-relaxed">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam et justo at enim iaculis feugiat et sit
                        amet lorem.
                    </p>
                    <a href="{{ url('/rooms') }}"
                        class="text-white bg-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 inline-flex items-center">
                        VIEW MORE
                        <svg class="w-3.5 h-3.5 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 14 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M1 5h12m0 0L9 1m4 4L9 9" />
                        </svg>
                    </a>
                </div>
                <div class="rounded-lg overflow-hidden shadow-xl">
                    <img src="{{ asset('img/kamar.png') }}" alt="Room & Suites"
                        class="w-full h-full object-cover transform hover:scale-105 transition duration-500">
                </div>
            </div>
        </div>
    </section>

    {{-- 4. MEETINGS & EVENTS SECTION --}}
    <section class="py-16 bg-white">
        <div class="max-w-screen-xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="rounded-lg overflow-hidden shadow-xl order-2 md:order-1">
                    <img src="{{ asset('img/kamar.png') }}" alt="Meetings & Events"
                        class="w-full h-full object-cover transform hover:scale-105 transition duration-500">
                </div>
                <div class="order-1 md:order-2">
                    <h3 class="text-3xl font-bold text-gray-900 mb-4">MEETINGS & EVENTS</h3>
                    <p class="text-gray-600 mb-6 text-lg leading-relaxed">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam et justo at enim iaculis feugiat et sit
                        amet lorem.
                    </p>
                    <a href="{{ url('/meetings-events') }}"
                        class="text-white bg-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 inline-flex items-center">
                        VIEW MORE
                        <svg class="w-3.5 h-3.5 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 14 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M1 5h12m0 0L9 1m4 4L9 9" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- SECTION TESTIMONIALS (ANIMASI SCROLL INFINITE) --}}
    <section id="testimoni" class="py-16 bg-gray-50 overflow-hidden" x-data="{ openModal: false }">
        <div class="max-w-screen-xl mx-auto px-4 mb-10">

            {{-- Header Section --}}
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">KATA MEREKA</h2>
                <div class="w-20 h-1 bg-yellow-400 mx-auto mt-4 rounded-full"></div>
                <p class="mt-4 text-gray-500">Pengalaman asli dari tamu Hotel Rumah RB</p>

                <button @click="openModal = true"
                    class="mt-6 bg-gray-900 text-white px-6 py-2 rounded-full text-sm font-semibold hover:bg-gray-800 transition shadow-lg flex items-center gap-2 mx-auto">
                    <i class="fa-solid fa-pen-nib"></i> Tulis Pengalaman Anda
                </button>
            </div>

        </div>

        {{-- AREA SCROLLING --}}
        @if(isset($testimonials) && count($testimonials) > 0)

            <style>
                @keyframes scroll {
                    0% {
                        transform: translateX(0);
                    }

                    100% {
                        transform: translateX(-50%);
                    }
                }

                .animate-scroll {
                    display: flex;
                    width: max-content;
                    animation: scroll 35s linear infinite;
                    /* Ubah angka 40s kalau mau lebih cepat/lambat */
                }

                /* Kalau mau berhenti saat di-hover, uncomment baris bawah ini */
                /* .animate-scroll:hover {
                    animation-play-state: paused;
                } */
            </style>

            <div class="relative w-full">
                {{-- Efek Blur Kiri Kanan --}}
                <div
                    class="absolute inset-y-0 left-0 w-12 md:w-32 bg-gradient-to-r from-gray-50 to-transparent z-10 pointer-events-none">
                </div>
                <div
                    class="absolute inset-y-0 right-0 w-12 md:w-32 bg-gradient-to-l from-gray-50 to-transparent z-10 pointer-events-none">
                </div>

                {{-- TRACK ANIMASI --}}
                <div class="animate-scroll gap-6 pl-4">

                    {{-- KITA LOOPING 2 KALI AGAR EFEKNYA NYAMBUNG TERUS --}}
                    @for ($i = 0; $i < 2; $i++)
                        @foreach($testimonials as $testi)
                            <div
                                class="w-[350px] md:w-[400px] flex-shrink-0 bg-white p-8 rounded-2xl shadow-sm border border-gray-100 relative">
                                {{-- Kutipan --}}
                                <div class="absolute top-4 right-6 text-gray-200 text-6xl font-serif">”</div>

                                {{-- Bintang --}}
                                <div class="flex text-yellow-400 mb-4 text-sm">
                                    @for($x = 0; $x < $testi->stars; $x++)
                                        <i class="fa-solid fa-star"></i>
                                    @endfor
                                </div>

                                {{-- Isi --}}
                                <p class="text-gray-600 italic mb-6 leading-relaxed text-sm line-clamp-4 h-20">
                                    "{{ $testi->content }}"
                                </p>

                                {{-- Profil --}}
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm border border-blue-200 shadow-sm">
                                        {{ substr($testi->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 text-sm">{{ $testi->name }}</h4>
                                        <span class="text-xs text-gray-400">Tamu Terverifikasi</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endfor
                </div>
            </div>

        @else
            <div class="text-center text-gray-400 py-10">
                <p>Belum ada review.</p>
            </div>
        @endif

        {{-- MODAL FORMULIR (SAMA SEPERTI SEBELUMNYA) --}}
        <div x-show="openModal" style="display: none;"
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
            x-transition.opacity>
            {{-- ... (Isi Form Modal Sama seperti sebelumnya) ... --}}
            <div @click.outside="openModal = false"
                class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all"
                x-transition.scale>
                <div class="bg-gray-900 text-white p-4 flex justify-between items-center">
                    <h3 class="font-bold text-lg">Bagikan Pengalaman Anda</h3>
                    <button @click="openModal = false" class="text-gray-400 hover:text-white"><i
                            class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                <form action="{{ route('testimoni.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ auth()->user()->name ?? '' }}" required
                            class="w-full border-gray-300 rounded-lg text-sm focus:ring-yellow-400 focus:border-yellow-400"
                            placeholder="Contoh: Budi Santoso">
                    </div>
                    <div x-data="{ rating: 5, hoverRating: 0 }">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                        <input type="hidden" name="stars" :value="rating">
                        <div class="flex items-center gap-1 cursor-pointer">
                            <template x-for="star in 5">
                                <i class="fa-star text-2xl transition-colors duration-200"
                                    :class="(star <= (hoverRating || rating)) ? 'fa-solid text-yellow-400' : 'fa-regular text-gray-300'"
                                    @click="rating = star" @mouseenter="hoverRating = star"
                                    @mouseleave="hoverRating = 0"></i>
                            </template>
                            <span class="ml-2 text-sm text-gray-500 font-medium" x-text="rating + ' Bintang'"></span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cerita Pengalaman</label>
                        <textarea name="content" rows="3" required
                            class="w-full border-gray-300 rounded-lg focus:ring-yellow-400 focus:border-yellow-400 text-sm"
                            placeholder="Kamar bersih..."></textarea>
                    </div>
                    <button type="submit"
                        class="w-full bg-yellow-400 text-white font-bold py-3 rounded-lg hover:bg-yellow-500 transition shadow-md">Kirim
                        Review</button>
                </form>
            </div>
        </div>
    </section>

@endsection