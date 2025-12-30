<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css" rel="stylesheet" />

{{-- WRAPPER ALPINE.JS (PENTING: x-data ini mengontrol pop-up) --}}
<div x-data="{ showGlobalBooking: false }">

    {{-- 1. BAGIAN ATAS (USER MENU / LOGIN) --}}
    {{-- PERBAIKAN: Tambahkan 'relative z-[60]' agar dropdown muncul DI ATAS navbar utama --}}
    <div
        class="relative z-[60] hidden md:flex items-center justify-end px-4 py-2 bg-gray-50 border-b border-gray-100 space-x-3">
        @guest
            {{-- Jika Belum Login --}}
            <a href="{{ route('login') }}"
                class="text-xs font-bold text-gray-600 hover:text-yellow-600 transition tracking-wide">
                SIGN IN <i class="fa-solid fa-right-to-bracket ml-1"></i>
            </a>
            <span class="text-gray-300">|</span>
            <a href="{{ route('register') }}"
                class="text-xs font-bold text-gray-600 hover:text-yellow-600 transition tracking-wide">
                REGISTER
            </a>
        @endguest
    
        @auth
            {{-- Jika Sudah Login (Dropdown Menu) --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                    class="flex items-center gap-2 text-sm font-bold text-gray-800 hover:text-yellow-600 transition">
                    Halo, {{ Auth::user()->name }}
                    <i class="fa-solid fa-chevron-down text-xs"></i>
                </button>

                {{-- Dropdown Content --}}
                <div x-show="open" @click.outside="open = false"
                    class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-50 overflow-hidden"
                    style="display: none;">

                    <a href="{{ route('booking.history') }}"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-yellow-50 hover:text-yellow-700 transition">
                        <i class="fa-solid fa-clock-rotate-left mr-2"></i> Riwayat Pesanan
                    </a>

                    <div class="border-t border-gray-100 my-1"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                            <i class="fa-solid fa-right-from-bracket mr-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        @endauth
    </div>

    {{-- 2. MAIN NAVBAR --}}
    <nav class="bg-white border-gray-200 shadow-sm sticky top-0 z-50">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">

            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex items-center space-x-3 rtl:space-x-reverse">
                <img src="{{ asset('img/logo-hotel.png') }}" class="h-12 md:h-16 w-auto" alt="Logo Hotel" />
            </a>

            {{-- Mobile Menu Button --}}
            <button data-collapse-toggle="navbar-default" type="button"
                class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200"
                aria-controls="navbar-default" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 17 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M1 1h15M1 7h15M1 13h15" />
                </svg>
            </button>

            {{-- Menu Links --}}
            <div class="hidden w-full md:block md:w-auto" id="navbar-default">
                <ul
                    class="font-medium flex flex-col p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0 md:bg-white items-center">
            
                    {{-- 1. HOME --}}
                    <li>
                        <a href="{{ url('/') }}"
                            class="block py-2 px-3 rounded md:p-0 transition-colors duration-300 {{ Request::is('/') ? 'text-yellow-500 font-bold' : 'text-gray-700 hover:text-yellow-600' }}">Home</a>
                    </li>
            
                    {{-- 2. ROOMS --}}
                    <li>
                        <a href="{{ url('/rooms') }}"
                            class="block py-2 px-3 rounded md:p-0 transition-colors duration-300 {{ Request::is('rooms*') ? 'text-yellow-600 font-bold' : 'text-gray-700 hover:text-yellow-600' }}">Rooms & Suite</a>
                    </li>
            
                    {{-- 3. MEETINGS (Ini yang tadi hilang) --}}
                    <li>
                        <a href="{{ url('/meetings-events') }}"
                            class="block py-2 px-3 rounded md:p-0 transition-colors duration-300 {{ Request::is('meetings-events*') ? 'text-yellow-600 font-bold' : 'text-gray-700 hover:text-yellow-600' }}">Meetings & Events</a>
                    </li>
            
                    {{-- 4. GALLERY --}}
                    <li>
                        <a href="{{ url('/gallery') }}"
                            class="block py-2 px-3 rounded md:p-0 transition-colors duration-300 {{ Request::is('gallery*') ? 'text-yellow-600 font-bold' : 'text-gray-700 hover:text-yellow-600' }}">Gallery</a>
                    </li>
            
                    {{-- 5. CONTACT (Ini juga tadi hilang) --}}
                    <li>
                        <a href="{{ url('/contact') }}"
                            class="block py-2 px-3 rounded md:p-0 transition-colors duration-300 {{ Request::is('contact*') ? 'text-yellow-600 font-bold' : 'text-gray-700 hover:text-yellow-600' }}">Contact</a>
                    </li>
            
                    {{-- 6. TOMBOL BOOKING --}}
                    <li class="mt-2 md:mt-0">
                        @guest
                            {{-- Jika Tamu (Belum Login) --}}
                            <a href="{{ route('login') }}"
                                class="block text-center text-white bg-yellow-500 hover:bg-yellow-900 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none transition-all shadow-md w-full md:w-auto uppercase">
                                Login to Book
                            </a>
                        @endguest
            
                        @auth
                            {{-- Jika Member (Sudah Login) --}}
                            <button @click="showGlobalBooking = true" type="button"
                                class="text-white bg-gray-900 hover:bg-yellow-600 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5 w-full md:w-auto uppercase">
                                Book Now
                            </button>
                        @endauth
                    </li>
            
                </ul>
            </div>
        </div>
    </nav>


    {{-- 3. GLOBAL BOOKING MODAL (POP-UP) --}}
    <div x-show="showGlobalBooking" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
        style="display: none;">

        <div @click.outside="showGlobalBooking = false"
            class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden relative">

            {{-- Header Modal --}}
            <div class="bg-gray-900 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white uppercase tracking-wider">Reservasi Kamar</h3>
                <button @click="showGlobalBooking = false" class="text-gray-400 hover:text-white transition">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>

            {{-- Body Modal --}}
            <div class="p-6">

                {{-- Alert Error (Jika Validasi Gagal) --}}
                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 text-sm">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                <form action="{{ route('booking.store') }}" method="POST" class="space-y-4">
                    @csrf

                    {{-- Pilihan Kamar --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Pilih Tipe Kamar</label>
                        <select name="tipe_kamar_manual" required
                            class="w-full bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-2 text-sm focus:border-yellow-500 outline-none font-bold text-gray-800">
                            <option value="" disabled selected>-- Pilih Kamar --</option>
                            <option value="Superior Room">Superior Room</option>
                            <option value="Deluxe Room">Deluxe Room</option>
                            <option value="Family Room">Family Room</option>
                        </select>
                    </div>

                    {{-- Nama (AUTO FILL DARI USER LOGIN) --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Lengkap</label>
                        <input type="text" name="nama_tamu" value="{{ Auth::user()->name ?? '' }}" required
                            placeholder="Nama Anda"
                            class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2 text-sm focus:border-yellow-500 outline-none font-medium text-gray-900">
                    </div>

                    {{-- No HP --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">WhatsApp</label>
                        <input type="tel" name="nomor_hp" required placeholder="0812..."
                            class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2 text-sm focus:border-yellow-500 outline-none">
                    </div>

                    {{-- Tanggal --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Check-In</label>
                            <input type="date" name="check_in" required
                                class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-yellow-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Check-Out</label>
                            <input type="date" name="check_out" required
                                class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-yellow-500 outline-none">
                        </div>
                    </div>

                    {{-- Jumlah Kamar --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Jumlah Kamar</label>
                        <select name="jumlah_kamar"
                            class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2 text-sm focus:border-yellow-500 outline-none">
                            <option value="1">1 Kamar</option>
                            <option value="2">2 Kamar</option>
                            <option value="3">3 Kamar</option>
                        </select>
                    </div>

                    {{-- Tombol Submit --}}
                    <button type="submit"
                        class="w-full bg-gray-900 text-white font-bold py-3 rounded-xl hover:bg-yellow-600 transition-all shadow-lg mt-2 flex justify-center items-center gap-2">
                        <span>KIRIM RESERVASI</span>
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>

                    <p class="text-[10px] text-center text-gray-400">*Admin akan menghubungi WA Anda untuk konfirmasi.
                    </p>
                </form>
            </div>
        </div>
    </div>

</div>
{{-- End Wrapper x-data --}}

<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>