<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">

    {{-- BACKGROUND IMAGE --}}
    {{-- Pastikan kamu punya gambar 'hotel-luar.png' atau ganti dengan gambar lain --}}
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-cover bg-center relative"
        style="background-image: url('{{ asset('img/hotel-luar.png') }}');">

        {{-- OVERLAY HITAM (Agar teks terbaca) --}}
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm z-0"></div>

        {{-- KONTEN --}}
        <div class="relative z-10 w-full flex flex-col items-center">

            {{-- LOGO --}}
            <div class="mb-6">
                <a>
                    <img src="{{ asset('img/logo-hotel.png') }}" alt="Logo Hotel Rumah RB"
                        class="w-48 h-auto drop-shadow-lg" />
                </a>
            </div>

            {{-- CARD FORMULIR --}}
            <div
                class="w-full sm:max-w-md px-8 py-8 bg-white/90 backdrop-blur-md shadow-2xl overflow-hidden sm:rounded-2xl border border-white/50">
                {{ $slot }}
            </div>

            {{-- COPYRIGHT --}}
            <div class="mt-8 text-white/80 text-sm">
                &copy; {{ date('Y') }} Hotel Rumah RB. All rights reserved.
            </div>
        </div>
    </div>
</body>

</html>