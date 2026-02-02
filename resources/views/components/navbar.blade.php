<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css" rel="stylesheet" />

{{-- WRAPPER UTAMA (Tanpa x-data modal lagi) --}}
<div class="contents">

    {{-- 1. TOP BAR --}}
    <div
        class="relative z-[60] hidden md:flex items-center justify-end px-4 py-2 bg-gray-50 border-b border-gray-100 space-x-3">
        @guest
            <a href="{{ route('login') }}"
                class="text-xs font-bold text-gray-600 hover:text-yellow-600 transition tracking-wide">SIGN IN</a>
            <span class="text-gray-300">|</span>
            <a href="{{ route('register') }}"
                class="text-xs font-bold text-gray-600 hover:text-yellow-600 transition tracking-wide">REGISTER</a>
        @endguest
        @auth
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                    class="flex items-center gap-2 text-sm font-bold text-gray-800 hover:text-yellow-600 transition">
                    Halo, {{ Auth::user()->name }} <i class="fa-solid fa-chevron-down text-xs"></i>
                </button>
                <div x-show="open" @click.outside="open = false"
                    class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-xl border border-gray-100 py-2 z-50 overflow-hidden"
                    style="display: none;">
                    <a href="{{ route('booking.history') }}"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-yellow-50 hover:text-yellow-700 transition">Riwayat
                        Pesanan</a>
                    <div class="border-t border-gray-100 my-1"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">Logout</button>
                    </form>
                </div>
            </div>
        @endauth
    </div>

    {{-- 2. MAIN NAVBAR (STICKY) --}}
    <nav class="bg-white border-gray-200 shadow-sm sticky top-0 z-50 w-full transition-all duration-300">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
            <a href="{{ url('/') }}" class="flex items-center space-x-3 rtl:space-x-reverse">
                <img src="{{ asset('img/logo-hotel.png') }}" class="h-12 md:h-16 w-auto" alt="Logo Hotel" />
            </a>

            <div class="hidden w-full md:block md:w-auto">
                <ul class="font-medium flex flex-col md:flex-row md:space-x-8 items-center">
                    <li><a href="{{ url('/') }}"
                            class="{{ Request::is('/') ? 'text-yellow-600 font-bold' : 'text-gray-700 hover:text-yellow-600 font-medium' }}">Home</a>
                    </li>
                    <li><a href="{{ url('/rooms') }}"
                            class="{{ Request::is('rooms*') ? 'text-yellow-600 font-bold' : 'text-gray-700 hover:text-yellow-600 font-medium' }}">Rooms
                            & Suite</a></li>
                    <li><a href="{{ url('/meetings-events') }}"
                            class="{{ Request::is('meetings-events*') ? 'text-yellow-600 font-bold' : 'text-gray-700 hover:text-yellow-600 font-medium' }}">Meetings
                            & Events</a></li>
                    <li><a href="{{ url('/gallery') }}"
                            class="{{ Request::is('gallery*') ? 'text-yellow-600 font-bold' : 'text-gray-700 hover:text-yellow-600 font-medium' }}">Gallery</a>
                    </li>
                    <li><a href="{{ url('/contact') }}"
                            class="{{ Request::is('contact*') ? 'text-yellow-600 font-bold' : 'text-gray-700 hover:text-yellow-600 font-medium' }}">Contact</a>
                    </li>
                    <li>
                        <a href="{{ route('booking.create') }}"
                            class="text-white bg-gray-900 hover:bg-yellow-600 px-6 py-2.5 rounded-md text-sm font-bold shadow-sm transition-all transform hover:-translate-y-0.5 uppercase tracking-wide">
                            Book Now
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

</div>

{{--
SCRIPT FLOWBITE BIARKAN DI SINI JIKA DIPERLUKAN NAVBAR.
TAPI SCRIPT SWEETALERT WAJIB DIHAPUS KARENA SUDAH ADA DI LAYOUT UTAMA (MAIN.BLADE.PHP)
--}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>