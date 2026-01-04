@extends('layouts.main')

@section('title', $room->tipe_kamar . ' - Hotel Rumah RB')

{{-- CSS KHUSUS --}}
@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/material_blue.css">
    <style>
        .flatpickr-calendar {
            font-family: inherit !important;
            border-radius: 4px !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
            z-index: 999999 !important;
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
@endsection

@section('content')

    {{-- HEADER GAMBAR --}}
    <div class="relative h-[60vh] w-full overflow-hidden">
        <img src="{{ asset('storage/' . $room->foto) }}" alt="{{ $room->tipe_kamar }}"
            class="w-full h-full object-cover object-center">
        <div class="absolute inset-0 bg-black/40"></div>
        <div class="absolute bottom-0 left-0 w-full p-8 md:p-12 bg-gradient-to-t from-black/90 to-transparent">
            <div class="max-w-screen-xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-extrabold text-white uppercase tracking-wide mb-2 shadow-sm">
                    {{ $room->tipe_kamar }}
                </h1>
                <p class="text-yellow-400 text-lg font-medium flex items-center gap-2">
                    <i class="fa-solid fa-star"></i> Recommended Room
                </p>
            </div>
        </div>
    </div>

    {{-- KONTEN UTAMA --}}
    <section class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-screen-xl mx-auto px-4">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

                {{-- KOLOM KIRI (Info Kamar) --}}
                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                        <h3 class="text-xl font-bold text-gray-900 mb-4 border-l-4 border-yellow-500 pl-4">DESCRIPTION</h3>
                        <p class="text-gray-600 leading-relaxed text-justify">
                            {{ $room->deskripsi }}
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 border-l-4 border-yellow-500 pl-4">AMENITIES</h3>
                        @if ($room->fasilitas)
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach (explode(',', $room->fasilitas) as $facility)
                                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                        <div class="text-yellow-500"><i class="fa-solid fa-check-circle"></i></div>
                                        <span class="text-gray-700 text-sm font-medium">{{ trim($facility) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- KOLOM KANAN (Sidebar Form Booking) --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 sticky top-24">

                        <p class="text-gray-500 text-xs font-bold uppercase mb-1">Price per Night</p>
                        <div class="flex items-baseline gap-1 mb-6 border-b border-gray-100 pb-6">
                            <span class="text-3xl font-bold text-gray-900">
                                Rp {{ number_format($room->harga, 0, ',', '.') }}
                            </span>
                        </div>

                        {{-- WRAPPER MODAL --}}
                        <div x-data="{ open: {{ session('error') || $errors->any() ? 'true' : 'false' }} }">

                            @guest
                                <a href="{{ route('login') }}"
                                    class="w-full block text-center bg-gray-200 text-gray-500 font-bold py-4 rounded-md uppercase tracking-wider hover:bg-gray-300 transition-all duration-300 shadow-inner mb-4">
                                    <i class="fa-solid fa-lock mr-2"></i> LOGIN TO BOOK
                                </a>
                                <p class="text-[10px] text-center text-gray-400 mb-4">Anda harus login terlebih dahulu.</p>
                            @endguest

                            @auth
                                <button @click="open = true"
                                    class="w-full bg-gray-900 text-white font-bold py-4 rounded-md uppercase tracking-wider hover:bg-yellow-600 transition-all duration-300 shadow-lg mb-4 flex justify-center items-center gap-2">
                                    <span>BOOK NOW</span>
                                </button>
                            @endauth

                            {{-- MODAL POP-UP --}}
                            <div x-show="open"
                                class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
                                style="display: none;">

                                {{-- PERUBAHAN: MENGHAPUS @click.outside --}}
                                <div class="bg-white rounded-lg shadow-2xl w-full max-w-2xl overflow-hidden relative transition-all transform max-h-[90vh] flex flex-col">

                                    {{-- Header --}}
                                    <div class="bg-gray-900 px-6 py-5 flex justify-between items-center flex-shrink-0">
                                        <div class="flex items-center gap-3">
                                            <div class="bg-yellow-500 p-2 rounded text-gray-900">
                                                <i class="fa-solid fa-bed text-lg"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-white uppercase tracking-wider">Booking {{ $room->tipe_kamar }}</h3>
                                                <p class="text-[10px] text-gray-300">Lengkapi data untuk reservasi</p>
                                            </div>
                                        </div>
                                        <button @click="open = false"
                                            class="text-gray-400 hover:text-white transition w-8 h-8 flex items-center justify-center rounded hover:bg-gray-700">
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

                                                {{-- KIRI --}}
                                                <div class="space-y-5">
                                                    {{-- Tipe Kamar (Read Only) --}}
                                                    <div>
                                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Tipe Kamar</label>
                                                        <div class="relative">
                                                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                                                <i class="fa-solid fa-bed"></i>
                                                            </div>
                                                            <input type="text" value="{{ $room->tipe_kamar }}" disabled
                                                                class="pl-10 w-full bg-gray-100 border border-gray-200 rounded-md px-4 py-3 text-sm font-bold text-gray-500 cursor-not-allowed">
                                                            <input type="hidden" name="kamar_id" value="{{ $room->id }}">
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
                                                                class="pl-10 w-full bg-gray-50 border border-gray-200 rounded-md px-4 py-3 text-sm font-medium text-gray-900 focus:ring-2 focus:ring-yellow-400 focus:border-transparent outline-none shadow-sm">
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
                                                                class="pl-10 w-full bg-gray-50 border border-gray-200 rounded-md px-4 py-3 text-sm font-medium text-gray-900 focus:ring-2 focus:ring-yellow-400 focus:border-transparent outline-none shadow-sm">
                                                        </div>
                                                    </div>

                                                    {{-- Jumlah Kamar --}}
                                                    <div>
                                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Jumlah Kamar</label>
                                                        <div class="relative">
                                                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                                                <i class="fa-solid fa-door-open"></i>
                                                            </div>
                                                            <select name="jumlah_kamar" class="pl-10 w-full bg-gray-50 border border-gray-200 rounded-md px-4 py-3 text-sm font-medium text-gray-900 focus:ring-2 focus:ring-yellow-400 focus:border-transparent outline-none appearance-none shadow-sm">
                                                                <option value="1">1 Kamar</option>
                                                                <option value="2">2 Kamar</option>
                                                                <option value="3">3 Kamar</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- KANAN (Kalender) --}}
                                                <div class="bg-blue-50/50 p-5 rounded-lg border border-blue-100 flex flex-col justify-between">
                                                    <div>
                                                        <h4 class="text-xs font-bold text-blue-800 uppercase mb-4 flex items-center gap-2">
                                                            <i class="fa-regular fa-calendar-days"></i> Jadwal Menginap
                                                        </h4>

                                                        <div class="space-y-4">
                                                            {{-- Check In Detail --}}
                                                            <div>
                                                                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Check-In</label>
                                                                <div class="relative">
                                                                    <input type="text" id="check_in_detail" name="check_in"
                                                                        required placeholder="Pilih Tanggal..." disabled
                                                                        class="w-full bg-white border border-blue-200 rounded-md px-4 py-3 text-sm font-bold text-gray-800 focus:ring-2 focus:ring-blue-400 outline-none disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed shadow-sm transition-all text-center placeholder-gray-400">
                                                                </div>
                                                                <p id="loading-text-detail" class="text-[10px] text-blue-600 hidden mt-1 animate-pulse font-bold text-center">
                                                                    <i class="fa-solid fa-spinner fa-spin mr-1"></i> Cek ketersediaan...
                                                                </p>
                                                            </div>

                                                            <div class="flex justify-center -my-2 relative z-10">
                                                                <div class="bg-blue-100 text-blue-600 rounded p-1 border-2 border-white">
                                                                    <i class="fa-solid fa-arrow-down text-xs"></i>
                                                                </div>
                                                            </div>

                                                            {{-- Check Out Detail --}}
                                                            <div>
                                                                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Check-Out</label>
                                                                <div class="relative">
                                                                    <input type="text" id="check_out_detail" name="check_out"
                                                                        required placeholder="Pilih Tanggal..." disabled
                                                                        class="w-full bg-white border border-blue-200 rounded-md px-4 py-3 text-sm font-bold text-gray-800 focus:ring-2 focus:ring-blue-400 outline-none disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed shadow-sm transition-all text-center placeholder-gray-400">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

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

                                            </div>

                                            <div class="border-t border-gray-100 pt-6 mt-4">
                                                <button type="submit"
                                                    class="w-full bg-gray-900 text-white font-bold py-4 rounded-md hover:bg-yellow-500 hover:text-gray-900 transition-all shadow-xl hover:shadow-yellow-200 transform hover:-translate-y-1 flex justify-center items-center gap-3 text-sm tracking-widest uppercase">
                                                    <span>Konfirmasi & Pesan</span>
                                                    <i class="fa-solid fa-paper-plane"></i>
                                                </button>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <a href="https://wa.me/6285777479609?text=Halo%20Admin,%20saya%20mau%20tanya%20tentang%20{{ urlencode($room->tipe_kamar) }}"
                            target="_blank"
                            class="flex justify-center items-center gap-2 text-green-600 font-bold hover:text-green-700 py-3 border border-green-200 rounded-md bg-green-50 hover:bg-green-100 transition-colors mt-4 text-sm">
                            <i class="fa-brands fa-whatsapp text-lg"></i> Chat Tanya Dulu
                        </a>

                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- SCRIPT KHUSUS DETAIL --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // --- LOGIKA FLATPICKR ---
            
            const kamarId = "{{ $room->id }}";
            const checkInInput = document.getElementById('check_in_detail');
            const checkOutInput = document.getElementById('check_out_detail');
            const loadingText = document.getElementById('loading-text-detail');
            let bookedDates = [];

            // Setup Check-in
            let checkInPicker = flatpickr(checkInInput, {
                minDate: "today",
                dateFormat: "Y-m-d",
                disable: [],

                // HOOK: Warnai tanggal penuh dengan MERAH
                onDayCreate: function (dObj, dStr, fp, dayElem) {
                    let dateStr = fp.formatDate(dayElem.dateObj, "Y-m-d");
                    if (bookedDates.includes(dateStr)) {
                        dayElem.classList.add('date-full');
                    }
                },

                onChange: function (selectedDates, dateStr, instance) {
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
                onDayCreate: function (dObj, dStr, fp, dayElem) {
                    let dateStr = fp.formatDate(dayElem.dateObj, "Y-m-d");
                    if (bookedDates.includes(dateStr)) {
                        dayElem.classList.add('date-full');
                    }
                }
            });

            // AUTO LOAD
            if (kamarId) {
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
            }
        });
    </script>

@endsection