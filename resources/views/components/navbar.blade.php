<script src="https://cdn.tailwindcss.com"></script>

{{-- WRAPPER UTAMA --}}
<div class="contents">

    {{-- 1. TOP BAR (Hanya Tampil di Laptop/PC - LG ke atas) --}}
    {{-- UBAH: md:flex jadi lg:flex --}}
    <div class="relative z-[60] hidden lg:flex items-center justify-end px-4 py-2 bg-gray-50 border-b border-gray-100 space-x-3">
        @guest
            <a href="{{ route('login') }}" class="text-xs font-bold text-gray-600 hover:text-yellow-600 transition tracking-wide">SIGN IN</a>
            <span class="text-gray-300">|</span>
            <a href="{{ route('register') }}" class="text-xs font-bold text-gray-600 hover:text-yellow-600 transition tracking-wide">REGISTER</a>
        @endguest
        @auth
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-2 text-sm font-bold text-gray-800 hover:text-yellow-600 transition">
                    Halo, {{ Auth::user()->name }} <i class="fa-solid fa-chevron-down text-xs"></i>
                </button>
                <div x-show="open" @click.outside="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-xl border border-gray-100 py-2 z-50" style="display: none;">
                    <a href="{{ route('booking.history') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-yellow-50 hover:text-yellow-700 transition">Riwayat Pesanan</a>
                    <div class="border-t border-gray-100 my-1"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">Logout</button>
                    </form>
                </div>
            </div>
        @endauth
    </div>

    {{-- 2. MAIN NAVBAR --}}
    <nav x-data="{ mobileMenuOpen: false }" class="bg-white border-gray-200 shadow-sm sticky top-0 z-50 w-full transition-all duration-300">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
            
            {{-- LOGO --}}
            <a href="{{ url('/') }}" class="flex items-center space-x-3 rtl:space-x-reverse">
                {{-- Logo sedikit lebih kecil di tablet --}}
                <img src="{{ asset('img/logo-hotel.png') }}" class="h-10 lg:h-16 w-auto" alt="Logo Hotel" />
            </a>

            {{-- TOMBOL HAMBURGER (Muncul di HP & Tablet) --}}
            {{-- UBAH: md:hidden jadi lg:hidden --}}
            <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="inline-flex items-center justify-center p-2 w-10 h-10 text-gray-500 rounded-lg lg:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all">
                <span class="sr-only">Open main menu</span>
                <div class="relative w-6 h-6">
                    <i class="fa-solid fa-bars text-xl absolute inset-0 transition-all duration-300 transform" 
                       :class="{'opacity-0 rotate-90': mobileMenuOpen, 'opacity-100 rotate-0': !mobileMenuOpen}"></i>
                    <i class="fa-solid fa-xmark text-2xl absolute inset-0 transition-all duration-300 transform" 
                       :class="{'opacity-100 rotate-0': mobileMenuOpen, 'opacity-0 -rotate-90': !mobileMenuOpen}"></i>
                </div>
            </button>

            {{-- MENU DESKTOP (Hanya Muncul di Laptop - LG ke atas) --}}
            {{-- UBAH: md:block jadi lg:block, md:w-auto jadi lg:w-auto --}}
            <div class="hidden w-full lg:block lg:w-auto">
                {{-- UBAH: md:flex-row jadi lg:flex-row, md:space-x-8 jadi lg:space-x-8 --}}
                <ul class="font-medium flex flex-col lg:flex-row lg:space-x-8 items-center">
                    <li><a href="{{ url('/') }}" class="{{ Request::is('/') ? 'text-yellow-600 font-bold' : 'text-gray-700 hover:text-yellow-600 font-medium' }}">Home</a></li>
                    <li><a href="{{ url('/rooms') }}" class="{{ Request::is('rooms*') ? 'text-yellow-600 font-bold' : 'text-gray-700 hover:text-yellow-600 font-medium' }}">Rooms & Suite</a></li>
                    <li><a href="{{ url('/meetings-events') }}" class="{{ Request::is('meetings-events*') ? 'text-yellow-600 font-bold' : 'text-gray-700 hover:text-yellow-600 font-medium' }}">Meetings & Events</a></li>
                    <li><a href="{{ url('/gallery') }}" class="{{ Request::is('gallery*') ? 'text-yellow-600 font-bold' : 'text-gray-700 hover:text-yellow-600 font-medium' }}">Gallery</a></li>
                    <li><a href="{{ url('/contact') }}" class="{{ Request::is('contact*') ? 'text-yellow-600 font-bold' : 'text-gray-700 hover:text-yellow-600 font-medium' }}">Contact</a></li>
                    <li>
                        <a href="{{ route('booking.create') }}" class="text-white bg-gray-900 hover:bg-yellow-600 px-6 py-2.5 rounded-md text-sm font-bold shadow-sm transition-all transform hover:-translate-y-0.5 uppercase tracking-wide">
                            Book Now
                        </a>
                    </li>
                </ul>
            </div>

            {{-- 
                === MENU MOBILE/TABLET === 
                Muncul di HP dan iPad.
                UBAH: md:hidden jadi lg:hidden 
            --}}
            <div x-show="mobileMenuOpen" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="lg:hidden w-full mt-4 bg-white border-t border-gray-100 shadow-lg rounded-xl overflow-hidden" 
                 style="display: none;">
                
                <ul class="flex flex-col p-4 font-medium space-y-1">
                    @foreach([
                        ['url' => '/', 'label' => 'Home', 'icon' => 'fa-house'],
                        ['url' => 'rooms', 'label' => 'Rooms & Suite', 'icon' => 'fa-bed'],
                        ['url' => 'meetings-events', 'label' => 'Meetings & Events', 'icon' => 'fa-handshake'],
                        ['url' => 'gallery', 'label' => 'Gallery', 'icon' => 'fa-images'],
                        ['url' => 'contact', 'label' => 'Contact', 'icon' => 'fa-envelope']
                    ] as $menu)
                        <li>
                            <a href="{{ url($menu['url']) }}" 
                               class="flex items-center gap-3 py-3 px-4 rounded-lg transition-colors
                               {{ Request::is($menu['url'] == '/' ? '/' : $menu['url'].'*') 
                                  ? 'bg-yellow-50 text-yellow-700 font-bold border-l-4 border-yellow-500' 
                                  : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                <i class="fa-solid {{ $menu['icon'] }} w-5 text-center"></i>
                                {{ $menu['label'] }}
                            </a>
                        </li>
                    @endforeach

                    {{-- User Section Mobile/Tablet --}}
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        @guest
                            <div class="grid grid-cols-2 gap-3">
                                <a href="{{ route('login') }}" class="text-center py-2.5 px-4 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-bold text-sm transition">Sign In</a>
                                <a href="{{ route('register') }}" class="text-center py-2.5 px-4 text-white bg-yellow-500 hover:bg-yellow-600 rounded-lg font-bold text-sm transition">Register</a>
                            </div>
                        @endguest

                        @auth
                            <div class="bg-gray-50 rounded-xl p-3">
                                <div class="flex items-center gap-3 px-2 mb-2">
                                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Halo,</p>
                                        <p class="text-sm font-bold text-gray-900">{{ Auth::user()->name }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('booking.history') }}" class="flex items-center gap-3 py-2 px-3 text-sm text-gray-700 hover:bg-white hover:shadow-sm rounded-lg transition">
                                    <i class="fa-solid fa-clock-rotate-left w-5 text-center text-yellow-600"></i> Riwayat Pesanan
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-3 py-2 px-3 text-sm text-red-600 hover:bg-white hover:shadow-sm rounded-lg transition text-left">
                                        <i class="fa-solid fa-arrow-right-from-bracket w-5 text-center"></i> Logout
                                    </button>
                                </form>
                            </div>
                        @endauth
                    </div>

                    <li class="pt-3">
                        <a href="{{ route('booking.create') }}" class="block w-full text-center text-white bg-gray-900 active:bg-gray-800 px-4 py-3 rounded-lg font-bold shadow-md uppercase tracking-wide">
                            Book Now
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>