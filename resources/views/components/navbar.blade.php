<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/material_blue.css">

<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css" rel="stylesheet" />

{{-- STYLE CSS CUSTOM --}}
<style>
    .flatpickr-calendar {
        font-family: inherit !important;
        border-radius: 4px !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        z-index: 100000 !important;
    }
    
    .flatpickr-day.date-full {
        background-color: #fef2f2 !important;
        color: #ef4444 !important;
        border: 1px solid #fee2e2 !important;
        font-weight: bold;
        cursor: not-allowed;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        line-height: 1.2 !important;
    }

    .flatpickr-day.date-full::after {
        content: 'PENUH';
        font-size: 7px;
        font-weight: 800;
        color: #dc2626;
        margin-top: 2px;
    }
</style>

{{-- WRAPPER ALPINE.JS --}}
<div x-data="{ showGlobalBooking: {{ session('error') || $errors->any() ? 'true' : 'false' }} }">

    {{-- 1. TOP BAR --}}
    <div class="relative z-[60] hidden md:flex items-center justify-end px-4 py-2 bg-gray-50 border-b border-gray-100 space-x-3">
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
                <div x-show="open" @click.outside="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-xl border border-gray-100 py-2 z-50 overflow-hidden" style="display: none;">
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
    <nav class="bg-white border-gray-200 shadow-sm sticky top-0 z-50">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
            <a href="{{ url('/') }}" class="flex items-center space-x-3 rtl:space-x-reverse">
                <img src="{{ asset('img/logo-hotel.png') }}" class="h-12 md:h-16 w-auto" alt="Logo Hotel" />
            </a>
            
            <div class="hidden w-full md:block md:w-auto">
                <ul class="font-medium flex flex-col md:flex-row md:space-x-8 items-center">
                    <li><a href="{{ url('/') }}" class="text-gray-700 hover:text-yellow-600 font-medium">Home</a></li>
                    <li><a href="{{ url('/rooms') }}" class="text-gray-700 hover:text-yellow-600 font-medium">Rooms & Suite</a></li>
                    <li><a href="{{ url('/meetings-events') }}" class="text-gray-700 hover:text-yellow-600 font-medium">Meetings & Events</a></li>
                    <li><a href="{{ url('/gallery') }}" class="text-gray-700 hover:text-yellow-600 font-medium">Gallery</a></li>
                    <li><a href="{{ url('/contact') }}" class="text-gray-700 hover:text-yellow-600 font-medium">Contact</a></li>
                    <li>
                        @auth
                            <button @click="showGlobalBooking = true" type="button" class="text-white bg-gray-900 hover:bg-yellow-600 px-6 py-2.5 rounded-md text-sm font-bold shadow-sm transition-all transform hover:-translate-y-0.5 uppercase tracking-wide">
                                Book Now
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="text-white bg-yellow-500 hover:bg-yellow-600 px-6 py-2.5 rounded-md text-sm font-bold shadow-sm transition-all transform hover:-translate-y-0.5 uppercase tracking-wide">
                                Login to Book
                            </a>
                        @endauth
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    {{-- 3. GLOBAL BOOKING MODAL --}}
    <div x-show="showGlobalBooking" 
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
        style="display: none;">

        {{-- PERUBAHAN DI SINI: --}}
        {{-- Saya MENGHAPUS @click.outside="..." --}}
        {{-- Sekarang modal TIDAK AKAN tertutup jika klik di luar --}}
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-2xl overflow-hidden relative transition-all transform max-h-[90vh] flex flex-col">
            
            {{-- Header --}}
            <div class="bg-gray-900 px-6 py-5 flex justify-between items-center flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="bg-yellow-500 p-2 rounded text-gray-900">
                        <i class="fa-solid fa-calendar-days text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white uppercase tracking-wider">Reservasi Kamar</h3>
                        <p class="text-[10px] text-gray-300">Isi form di bawah untuk memesan</p>
                    </div>
                </div>
                {{-- Hanya tombol ini yang bisa menutup modal --}}
                <button @click="showGlobalBooking = false" class="text-gray-400 hover:text-white transition w-8 h-8 flex items-center justify-center rounded hover:bg-gray-700">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>

            {{-- Body --}}
            <div class="p-8 overflow-y-auto">
                @if (session('error'))
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 mb-6 text-sm rounded-sm">
                        <strong class="font-bold">Gagal:</strong> {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('booking.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        
                        {{-- KOLOM KIRI --}}
                        <div class="space-y-5">
                            {{-- Pilih Kamar --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Pilih Tipe Kamar</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                        <i class="fa-solid fa-bed"></i>
                                    </div>
                                    <select id="kamar_id_navbar" name="kamar_id" required
                                        class="pl-10 w-full bg-gray-50 border border-gray-200 rounded-md px-4 py-3 text-sm font-bold text-gray-800 focus:ring-2 focus:ring-yellow-400 focus:border-transparent outline-none transition-all shadow-sm">
                                        <option value="" disabled selected>-- Pilih Kamar --</option>
                                        @foreach(\App\Models\Kamar::all() as $kamar)
                                            <option value="{{ $kamar->id }}">{{ $kamar->tipe_kamar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Nama --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nama Lengkap</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                        <i class="fa-solid fa-user"></i>
                                    </div>
                                    <input type="text" name="nama_tamu" value="{{ Auth::user()->name ?? '' }}" required
                                        class="pl-10 w-full bg-gray-50 border border-gray-200 rounded-md px-4 py-3 text-sm font-medium text-gray-900 focus:ring-2 focus:ring-yellow-400 focus:border-transparent outline-none transition-all shadow-sm">
                                </div>
                            </div>

                            {{-- WhatsApp --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">WhatsApp</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                        <i class="fa-brands fa-whatsapp"></i>
                                    </div>
                                    <input type="tel" name="nomor_hp" required placeholder="08..."
                                        class="pl-10 w-full bg-gray-50 border border-gray-200 rounded-md px-4 py-3 text-sm font-medium text-gray-900 focus:ring-2 focus:ring-yellow-400 focus:border-transparent outline-none transition-all shadow-sm">
                                </div>
                            </div>

                            {{-- Jumlah Kamar --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Jumlah Kamar</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                        <i class="fa-solid fa-door-open"></i>
                                    </div>
                                    <select name="jumlah_kamar" class="pl-10 w-full bg-gray-50 border border-gray-200 rounded-md px-4 py-3 text-sm font-medium text-gray-900 focus:ring-2 focus:ring-yellow-400 focus:border-transparent outline-none appearance-none transition-all shadow-sm">
                                        <option value="1">1 Kamar</option>
                                        <option value="2">2 Kamar</option>
                                        <option value="3">3 Kamar</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- KOLOM KANAN: Tanggal --}}
                        <div class="bg-blue-50/50 p-5 rounded-lg border border-blue-100 flex flex-col justify-between">
                            <div>
                                <h4 class="text-xs font-bold text-blue-800 uppercase mb-4 flex items-center gap-2">
                                    <i class="fa-regular fa-calendar-days"></i> Jadwal Menginap
                                </h4>
                                
                                <div class="space-y-4">
                                    {{-- Check In Navbar --}}
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Check-In</label>
                                        <div class="relative">
                                            <input type="text" id="check_in_navbar" name="check_in" required placeholder="Pilih Tanggal..." disabled
                                                class="w-full bg-white border border-blue-200 rounded-md px-4 py-3 text-sm font-bold text-gray-800 focus:ring-2 focus:ring-blue-400 outline-none disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed shadow-sm transition-all text-center placeholder-gray-400">
                                        </div>
                                        <p id="loading-text-navbar" class="text-[10px] text-blue-600 hidden mt-1 animate-pulse font-bold text-center">
                                            <i class="fa-solid fa-spinner fa-spin mr-1"></i> Cek ketersediaan...
                                        </p>
                                    </div>

                                    <div class="flex justify-center -my-2 relative z-10">
                                        <div class="bg-blue-100 text-blue-600 rounded p-1 border-2 border-white">
                                            <i class="fa-solid fa-arrow-down text-xs"></i>
                                        </div>
                                    </div>

                                    {{-- Check Out Navbar --}}
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Check-Out</label>
                                        <div class="relative">
                                            <input type="text" id="check_out_navbar" name="check_out" required placeholder="Pilih Tanggal..." disabled
                                                class="w-full bg-white border border-blue-200 rounded-md px-4 py-3 text-sm font-bold text-gray-800 focus:ring-2 focus:ring-blue-400 outline-none disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed shadow-sm transition-all text-center placeholder-gray-400">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- LEGENDA --}}
                            <div class="mt-4 pt-4 border-t border-blue-200">
                                <div class="flex justify-between items-center text-[10px] text-gray-500">
                                    <div class="flex items-center gap-1">
                                        <div class="w-2 h-2 rounded bg-white border border-gray-400"></div>
                                        <span>Available</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <div class="w-2 h-2 rounded bg-red-100 border border-red-300"></div>
                                        <span class="font-bold text-red-500">Penuh</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div> {{-- End Grid --}}

                    <div class="border-t border-gray-100 pt-6 mt-4">
                        <button type="submit" class="w-full bg-gray-900 text-white font-bold py-4 rounded-md hover:bg-yellow-500 hover:text-gray-900 transition-all shadow-xl hover:shadow-yellow-200 transform hover:-translate-y-1 flex justify-center items-center gap-3 text-sm tracking-widest uppercase">
                            <span>Konfirmasi & Pesan</span>
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const kamarSelect = document.getElementById('kamar_id_navbar');
    const checkInInput = document.getElementById('check_in_navbar');
    const checkOutInput = document.getElementById('check_out_navbar');
    const loadingText = document.getElementById('loading-text-navbar');

    let bookedDates = [];

    // Setup Check-in
    let checkInPicker = flatpickr(checkInInput, {
        minDate: "today",
        dateFormat: "Y-m-d",
        disable: [], 
        
        onDayCreate: function(dObj, dStr, fp, dayElem) {
            let dateStr = fp.formatDate(dayElem.dateObj, "Y-m-d");
            if (bookedDates.includes(dateStr)) {
                dayElem.classList.add('date-full');
            }
        },

        onChange: function(selectedDates, dateStr, instance) {
            checkOutPicker.set('minDate', dateStr);
            let nextDay = new Date(selectedDates[0]);
            nextDay.setDate(nextDay.getDate() + 1);
            checkOutPicker.setDate(nextDay);
            
            checkOutInput.disabled = false;
            checkOutInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
            checkOutInput.placeholder = "Pilih tanggal pulang";
            
            setTimeout(() => checkOutPicker.open(), 100);
        }
    });

    // Setup Check-out
    let checkOutPicker = flatpickr(checkOutInput, {
        minDate: "today",
        dateFormat: "Y-m-d",
        onDayCreate: function(dObj, dStr, fp, dayElem) {
            let dateStr = fp.formatDate(dayElem.dateObj, "Y-m-d");
            if (bookedDates.includes(dateStr)) {
                dayElem.classList.add('date-full');
            }
        }
    });

    // Logic saat ganti kamar
    kamarSelect.addEventListener('change', function() {
        let kamarId = this.value;
        if(!kamarId) return;

        checkInInput.disabled = true;
        checkInInput.value = "";
        checkInInput.classList.add('bg-gray-100', 'cursor-not-allowed');
        
        checkOutInput.disabled = true;
        checkOutInput.value = "";
        
        loadingText.classList.remove('hidden');

        fetch(`/api/check-availability?kamar_id=${kamarId}`)
            .then(response => response.json())
            .then(data => {
                bookedDates = data;
                checkInPicker.set('disable', data);
                checkOutPicker.set('disable', data);

                checkInInput.disabled = false;
                checkInInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
                checkInInput.placeholder = "Pilih tanggal masuk";
                
                loadingText.classList.add('hidden');
            })
            .catch(error => {
                console.error('Error:', error);
                loadingText.textContent = "Gagal memuat data.";
            });
    });
});
</script>