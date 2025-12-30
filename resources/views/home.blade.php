@extends('layouts.main')
{{-- Pastikan extends ke layouts.app (bukan main) sesuai langkah sebelumnya --}}

@section('title', 'Home - Hotel Rumah RB')

@section('content')

    {{-- HERO SECTION --}}
    <section class="relative w-full h-[500px] md:h-[600px] overflow-hidden">
        <img src="{{ asset('img/hotel-luar.png') }}" alt="Hotel Rumah RB Exterior"
            class="absolute inset-0 w-full h-full object-cover">

        <div class="absolute inset-0 bg-black/30"></div>

        {{-- <div class="absolute inset-0 flex items-center justify-center">
            <h1 class="text-4xl md:text-6xl font-bold text-white shadow-lg">Welcome to Rumah RB</h1>
        </div> --}}
    </section>


    {{-- EXPERIENCE SECTION --}}
    <section class="py-16 bg-white">
        <div class="max-w-screen-xl mx-auto px-4">

            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Rumah RB Experience</h2>
                <div class="w-24 h-1 bg-yellow-400 mx-auto mb-6"></div>

                <p class="text-gray-500 max-w-3xl mx-auto text-lg">
                    Discover the traditional Padang escape at Hotel Rumah RB, where distinctive hotel experiences reflect
                    the
                    people most desirable destinations. From secluded getaway retreats to family-friendly destinations,
                    there
                    are endless ways to find your place in the sun.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

                <div class="text-center p-6 bg-gray-50 rounded-lg hover:shadow-lg transition-shadow duration-300">
                    <div
                        class="w-16 h-16 mx-auto bg-blue-100 rounded-full flex items-center justify-center mb-4 text-yellow-400 text-2xl">
                        <i class="fa-solid fa-square-parking"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-2 text-gray-900">On-site Parking</h4>
                    <p class="text-gray-600">Enjoy the comfort of our spacious and secure on-site parking area.</p>
                </div>

                <div class="text-center p-6 bg-gray-50 rounded-lg hover:shadow-lg transition-shadow duration-300">
                    <div
                        class="w-16 h-16 mx-auto bg-blue-100 rounded-full flex items-center justify-center mb-4 text-yellow-400 text-2xl">
                        <i class="fa-solid fa-wifi"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-2 text-gray-900">Wi-Fi</h4>
                    <p class="text-gray-600">Enjoy fast, free Wi-Fi in all rooms and common areas.</p>
                </div>

                <div class="text-center p-6 bg-gray-50 rounded-lg hover:shadow-lg transition-shadow duration-300">
                    <div
                        class="w-16 h-16 mx-auto bg-blue-100 rounded-full flex items-center justify-center mb-4 text-yellow-400 text-2xl">
                        <i class="fa-solid fa-car-side"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-2 text-gray-900">Car Wash</h4>
                    <p class="text-gray-600">Car wash service is available exclusively for guests bringing their own
                        vehicles.</p>
                </div>

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


    {{-- ROOM & SUITES SECTION (Text Kiri, Gambar Kanan) --}}
    <section class="py-16 bg-gray-50">
        <div class="max-w-screen-xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">

                <div>
                    <h3 class="text-3xl font-bold text-gray-900 mb-4">ROOM & SUITES</h3>
                    <p class="text-gray-600 mb-6 text-lg leading-relaxed">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam et justo at enim iaculis feugiat et sit
                        amet
                        lorem. Suspendisse ac faucibus ipsum, maximus laoreet mi. Nullam pretium faucibus arcu.
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


    {{-- MEETINGS & EVENTS SECTION (Gambar Kiri, Text Kanan) --}}
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
                        amet
                        lorem. Suspendisse ac faucibus ipsum, maximus laoreet mi. Nulla pretium faucibus arcu.
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

@endsection