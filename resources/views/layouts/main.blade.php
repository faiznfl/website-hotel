<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- === 1. FAVICON (LOGO TAB) === --}}
    {{-- Ganti 'img/logo-hotel.png' dengan nama file logomu --}}
    <link rel="icon" href="{{ asset('img/logo-hotel-1.png') }}" type="image/png">
    <link rel="shortcut icon" href="{{ asset('img/logo-hotel-1.png') }}" type="image/png">

    {{-- 2. CSS IZITOAST --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <title>@yield('title', 'Hotel Rumah RB - Padang')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50 text-gray-900 antialiased font-sans flex flex-col min-h-screen">

    @include('components.navbar')

    <main class="flex-grow">
        @yield('content')
    </main>

    @include('components.footer')

    {{-- 2. JS IZITOAST --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>

    <script>
        // Setting Global
        iziToast.settings({
            timeout: 4000,
            resetOnHover: true,
            icon: 'fa-solid', // Pakai fontawesome
            transitionIn: 'flipInX',
            transitionOut: 'flipOutX',
            position: 'topRight', // Kanan Atas
            progressBarColor: 'rgb(255, 255, 255)',
            theme: 'light',
        });

        @if (session('success'))
            iziToast.success({
                title: 'Berhasil',
                message: "{{ session('success') }}",
                color: 'green',
                icon: 'fa-solid fa-check',
            });
        @endif

        @if (session('error'))
            iziToast.error({
                title: 'Error',
                message: "{{ session('error') }}",
                color: 'red',
                icon: 'fa-solid fa-circle-exclamation',
            });
        @endif
    </script>
</body>

</html>