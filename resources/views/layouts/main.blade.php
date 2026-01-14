<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    {{-- Alpine.js (Untuk interaksi ringan seperti Flash Message) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    
    {{-- Vite Assets (Tailwind & JS Bawaan Laravel) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <title>@yield('title', 'Hotel Rumah RB - Padang')</title>

    {{-- FontAwesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50 text-gray-900 antialiased font-sans">

    {{-- 1. Panggil Navbar (Pastikan path-nya benar) --}}
    {{-- Jika file ada di views/layouts/navbar.blade.php --}}
    @include('components.navbar') 

    {{-- 2. Konten Utama --}}
    <main class="min-h-screen pt-[80px]"> {{-- Tambah padding-top biar konten tidak ketutup Navbar Sticky --}}
        @yield('content')
    </main>

    {{-- 3. Footer --}}
    @include('components.footer')

    {{-- 4. GLOBAL SUCCESS MESSAGE (Toast Pojok Kanan Atas) --}}
    {{-- Ini hanya muncul jika Booking/Contact BERHASIL --}}
    @if (session('success'))
        <div x-data="{ show: true }" 
             x-init="setTimeout(() => show = false, 5000)" 
             x-show="show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-8"
             x-transition:enter-end="opacity-100 transform translate-x-0" 
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform translate-x-0"
             x-transition:leave-end="opacity-0 transform translate-x-8"
             class="fixed top-24 right-5 z-[100002] bg-white border-l-4 border-green-500 text-gray-800 px-6 py-4 rounded-lg shadow-2xl flex items-center gap-4 max-w-sm md:max-w-md pointer-events-auto">

            <div class="bg-green-100 text-green-500 rounded-full p-2 flex-shrink-0">
                <i class="fa-solid fa-check text-xl"></i>
            </div>

            <div>
                <h4 class="font-bold text-sm uppercase text-green-600">Berhasil!</h4>
                <p class="text-xs text-gray-600 leading-relaxed">
                    {{ session('success') }}
                </p>
            </div>

            <button @click="show = false" class="text-gray-400 hover:text-gray-900 transition ml-auto">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
    @endif
    
    {{-- NOTE: Error Message tidak perlu ditaruh di sini. --}}
    {{-- Karena sudah ditangani oleh SweetAlert di dalam Navbar. --}}

</body>
</html>