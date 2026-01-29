@extends('layouts.main')

@section('title', 'Booking Kamar - Hotel Rumah RB')

@section('content')
    {{-- 1. LOAD LIBRARY & CUSTOM CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    <style>
        /* --- CUSTOM SCROLLBAR --- */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #9ca3af; }

        /* --- FLATPICKR LUXURY THEME --- */
        .flatpickr-calendar {
            border: none !important;
            border-radius: 16px !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
            font-family: 'Figtree', sans-serif !important;
            padding: 12px !important;
            background: #ffffff !important;
            z-index: 9999 !important;
            width: auto !important;
        }
        .flatpickr-innerContainer { overflow: visible !important; }
        
        /* HEADER BULAN */
        .flatpickr-months .flatpickr-month {
            color: #111827 !important;
            fill: #111827 !important;
            margin-bottom: 10px;
        }

        /* TANGGAL BIASA */
        .flatpickr-day {
            border-radius: 8px !important;
            font-weight: 600 !important;
            color: #374151 !important; /* Warna default (Hitam Abu) */
            border: 1px solid transparent !important;
            margin: 2px !important;
            width: 38px !important;
            height: 38px !important;
            line-height: 38px !important;
        }

        /* TANGGAL DISABILIT (LEWAT) - PERBAIKAN DISINI */
        .flatpickr-day.flatpickr-disabled,
        .flatpickr-day.flatpickr-disabled:hover {
            color: #d1d5db !important; /* Abu-abu muda banget */
            background: transparent !important;
            border-color: transparent !important;
            cursor: not-allowed !important;
        }

        /* TANGGAL TERPILIH */
        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange {
            background: #CA8A04 !important; /* Yellow-600 */
            color: white !important;
            border-color: #CA8A04 !important;
        }

        /* TANGGAL DALAM RANGE */
        .flatpickr-day.inRange {
            background: #FEF3C7 !important;
            border-color: #FEF3C7 !important;
            color: #854D0E !important;
            box-shadow: -5px 0 0 #FEF3C7, 5px 0 0 #FEF3C7 !important;
        }

        /* HOVER TANGGAL AKTIF */
        .flatpickr-day:not(.flatpickr-disabled):hover {
            background: #FEF9C3 !important;
        }
        
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; margin: 0; 
        }

        /* Animasi Custom Dropdown */
        .custom-options {
            transform-origin: top;
            transition: all 0.2s ease-in-out;
            transform: scaleY(0);
            opacity: 0;
            pointer-events: none;
        }
        .custom-options.show {
            transform: scaleY(1);
            opacity: 1;
            pointer-events: auto;
        }
        .option-item:hover {
            background-color: #FEFCE8;
        }
    </style>

    {{-- 2. BACKGROUND & LAYOUT --}}
    <div class="min-h-screen bg-gray-50 pb-24">
        
        {{-- Header Background --}}
        <div class="bg-[#0f172a] pb-32 pt-32 md:pt-40">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-4 mb-6">
                    <a href="{{ route('rooms.index') }}" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white hover:bg-yellow-500 hover:text-black transition-all">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-extrabold text-white tracking-tight">Selesaikan Pesanan</h1>
                        <p class="text-gray-400 text-sm">Langkah terakhir menuju liburan impian Anda.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- KOLOM KIRI: FORMULIR --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    <form action="{{ route('booking.store') }}" method="POST" id="bookingForm">
                        @csrf
                        <input type="hidden" name="total_harga" id="input_total_harga">
                        <input type="hidden" name="jumlah_kamar" id="jumlah_kamar_hidden" value="1">

                        {{-- SECTION 1: DETAIL MENGINAP --}}
                        <div class="bg-white rounded-3xl shadow-xl p-6 md:p-8 mb-6 relative overflow-visible z-20">
                            
                            {{-- Judul --}}
                            <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                                <span class="w-2 h-6 bg-yellow-500 rounded-full"></span>
                                Detail Menginap
                            </h3>

                            <div class="space-y-6">
                                
                                {{-- ROW 1: CUSTOM DROPDOWN KAMAR & JUMLAH --}}
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    
                                    {{-- Custom Dropdown Kamar (Lebar 2/3) --}}
                                    <div class="md:col-span-2 relative z-50">
                                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 block">Pilih Tipe Kamar</label>
                                        
                                        {{-- Hidden Select --}}
                                        <select name="kamar_id" id="kamar_select" class="hidden">
                                            <option value="" data-harga="0" data-foto="">-- Pilih Kamar --</option>
                                            @foreach($rooms as $r)
                                                <option value="{{ $r->id }}" 
                                                        data-harga="{{ $r->harga }}" 
                                                        data-foto="{{ asset('storage/' . $r->foto) }}"
                                                        data-tipe="{{ $r->tipe_kamar }}"
                                                        {{ (isset($selectedRoom) && $selectedRoom->id == $r->id) ? 'selected' : '' }}>
                                                    {{ $r->tipe_kamar }}
                                                </option>
                                            @endforeach
                                        </select>

                                        {{-- Trigger --}}
                                        <div class="relative">
                                            <div id="custom_trigger" class="w-full h-[60px] bg-gray-50 rounded-2xl px-5 flex items-center justify-between cursor-pointer border border-transparent hover:border-yellow-400 transition-all group shadow-sm">
                                                <div class="flex flex-col">
                                                    <span id="trigger_label" class="text-gray-900 font-bold text-lg leading-tight truncate">
                                                        {{ isset($selectedRoom) ? $selectedRoom->tipe_kamar : '-- Pilih Kamar --' }}
                                                    </span>
                                                    <span id="trigger_price" class="text-[10px] text-gray-500 font-medium">
                                                        {{ isset($selectedRoom) ? 'Rp ' . number_format($selectedRoom->harga, 0, ',', '.') . '/malam' : 'Klik untuk memilih' }}
                                                    </span>
                                                </div>
                                                <i class="fa-solid fa-chevron-down text-gray-400 group-hover:text-yellow-600 transition-colors"></i>
                                            </div>

                                            <div id="custom_options" class="custom-options absolute top-full left-0 w-full mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden max-h-60 overflow-y-auto z-[9999]">
                                                @foreach($rooms as $r)
                                                    <div class="option-item p-4 cursor-pointer border-b border-gray-50 last:border-0 flex items-center justify-between group transition-colors"
                                                         onclick="selectOption('{{ $r->id }}', '{{ $r->tipe_kamar }}', '{{ $r->harga }}')">
                                                        <div>
                                                            <div class="font-bold text-gray-800 group-hover:text-yellow-700">{{ $r->tipe_kamar }}</div>
                                                            <div class="text-xs text-gray-500">Rp {{ number_format($r->harga, 0, ',', '.') }} <span class="text-gray-300">/ malam</span></div>
                                                        </div>
                                                        <i class="fa-solid fa-check text-yellow-600 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Jumlah Kamar (Lebar 1/3) --}}
                                    <div class="md:col-span-1">
                                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 block">Jml. Unit</label>
                                        <div class="flex items-center bg-gray-50 rounded-2xl shadow-sm h-[60px] px-2 border border-transparent hover:border-yellow-200 transition-colors">
                                            <button type="button" id="btnMinus" class="w-10 h-full flex items-center justify-center text-gray-500 hover:text-yellow-600 transition-colors disabled:opacity-30">
                                                <i class="fa-solid fa-minus"></i>
                                            </button>
                                            
                                            <input type="number" id="jumlah_kamar_display" value="1" min="1" max="5" readonly
                                                class="flex-1 text-center border-0 bg-transparent text-lg font-bold text-gray-900 focus:ring-0 p-0 h-full">
                                            
                                            <button type="button" id="btnPlus" class="w-10 h-full flex items-center justify-center text-gray-500 hover:text-yellow-600 transition-colors disabled:opacity-30">
                                                <i class="fa-solid fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- ROW 2: TANGGAL --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                                    {{-- Check-in Container --}}
                                    <div id="check_in_container" class="relative group cursor-pointer transition-transform active:scale-95">
                                        <div class="absolute inset-0 bg-gray-50 rounded-2xl transition-colors group-hover:bg-gray-100 border border-transparent group-hover:border-yellow-200"></div>
                                        <div class="relative p-4 flex items-center gap-4 h-[70px]">
                                            <div class="w-10 h-10 rounded-xl bg-yellow-100 text-yellow-600 flex items-center justify-center text-lg">
                                                <i class="fa-regular fa-calendar-plus"></i>
                                            </div>
                                            <div class="flex-grow">
                                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-0.5 cursor-pointer">Check-In</label>
                                                <input type="text" name="check_in" id="check_in" required placeholder="Pilih Tanggal" readonly
                                                    class="w-full bg-transparent border-0 p-0 text-gray-900 font-bold text-base placeholder-gray-300 focus:ring-0 cursor-pointer h-6 pointer-events-none">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Check-out Container --}}
                                    <div id="check_out_container" class="relative group cursor-not-allowed transition-transform active:scale-95">
                                        <div id="check_out_bg" class="absolute inset-0 bg-gray-50 rounded-2xl transition-colors border border-transparent"></div>
                                        <div class="relative p-4 flex items-center gap-4 h-[70px]">
                                            <div id="check_out_icon" class="w-10 h-10 rounded-xl bg-gray-100 text-gray-400 flex items-center justify-center text-lg transition-colors">
                                                <i class="fa-regular fa-calendar-minus"></i>
                                            </div>
                                            <div class="flex-grow">
                                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-0.5 cursor-pointer">Check-Out</label>
                                                <input type="text" name="check_out" id="check_out" required placeholder="-" disabled readonly
                                                    class="w-full bg-transparent border-0 p-0 text-gray-900 font-bold text-base placeholder-gray-300 focus:ring-0 cursor-pointer h-6 pointer-events-none">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- SECTION 2: DATA DIRI --}}
                        <div class="bg-white rounded-3xl shadow-xl p-6 md:p-8 relative overflow-hidden z-10">
                            <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                                <span class="w-2 h-6 bg-gray-900 rounded-full"></span>
                                Data Pemesan
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 block">Nama Lengkap</label>
                                    <input type="text" name="nama_tamu" value="{{ Auth::user()->name }}" required
                                        class="w-full bg-gray-50 border-0 rounded-xl px-4 py-3 text-gray-900 font-semibold focus:ring-2 focus:ring-yellow-400">
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 block">Nomor WhatsApp</label>
                                    <input type="tel" name="nomor_hp" value="{{ Auth::user()->nomor_hp ?? '' }}" placeholder="08..." required
                                        class="w-full bg-gray-50 border-0 rounded-xl px-4 py-3 text-gray-900 font-semibold focus:ring-2 focus:ring-yellow-400">
                                </div>
                            </div>
                        </div>

                        {{-- TOMBOL SUBMIT (MOBILE) --}}
                        <div class="lg:hidden mt-6">
                            <button type="submit" id="btnSubmitMobile" disabled
                                class="w-full bg-gray-800 text-white font-bold py-4 rounded-2xl shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                                Lengkapi Data Dulu
                            </button>
                        </div>
                    </form>
                </div>

                {{-- KOLOM KANAN: PREVIEW CARD --}}
                <div class="lg:col-span-1">
                    <div class="sticky top-24">
                        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden ring-1 ring-gray-100">
                            
                            {{-- Image Area --}}
                            <div class="h-56 relative bg-gray-200">
                                <img id="preview_foto" src="{{ isset($selectedRoom) ? asset('storage/' . $selectedRoom->foto) : '' }}" 
                                    class="w-full h-full object-cover transition-opacity duration-500 {{ isset($selectedRoom) ? 'opacity-100' : 'opacity-0' }}">
                                
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                                <div class="absolute bottom-5 left-6 right-6">
                                    <span class="text-yellow-400 text-xs font-bold uppercase tracking-wider mb-1 block">Pilihan Anda</span>
                                    <h3 id="preview_tipe" class="text-white font-extrabold text-2xl leading-tight">
                                        {{ isset($selectedRoom) ? $selectedRoom->tipe_kamar : 'Pilih Kamar Dulu' }}
                                    </h3>
                                </div>

                                <div id="no_preview" class="absolute inset-0 flex items-center justify-center {{ isset($selectedRoom) ? 'hidden' : '' }}">
                                    <i class="fa-solid fa-image text-4xl text-gray-400 opacity-50"></i>
                                </div>
                            </div>

                            {{-- Payment Details --}}
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <span class="text-gray-500 text-sm">Harga / malam</span>
                                    <span class="font-bold text-gray-900" id="preview_harga">
                                        {{ isset($selectedRoom) ? 'Rp ' . number_format($selectedRoom->harga, 0, ',', '.') : '-' }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center mb-4">
                                    <span class="text-gray-500 text-sm">Jumlah Unit</span>
                                    <span class="font-bold text-gray-900 bg-yellow-100 text-yellow-800 px-3 py-1 rounded-lg text-sm" id="txt_qty">1 Unit</span>
                                </div>
                                <div class="flex justify-between items-center mb-6">
                                    <span class="text-gray-500 text-sm">Durasi</span>
                                    <span class="font-bold text-gray-900 bg-gray-100 px-3 py-1 rounded-lg text-sm" id="txt_durasi">0 Malam</span>
                                </div>
                                
                                <div class="border-t border-dashed border-gray-200 my-4"></div>

                                <div class="flex justify-between items-end mb-6">
                                    <div>
                                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Total Bayar</p>
                                        <p class="text-[10px] text-green-600 font-semibold">Termasuk Pajak</p>
                                    </div>
                                    <div class="text-3xl font-black text-gray-900 tracking-tight" id="txt_total">Rp 0</div>
                                </div>

                                <button type="submit" form="bookingForm" id="btnSubmitDesktop" disabled
                                    class="w-full bg-yellow-500 hover:bg-yellow-400 text-black font-extrabold py-4 rounded-2xl shadow-lg shadow-yellow-500/20 transform hover:-translate-y-1 transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none flex justify-center items-center gap-2">
                                    <span>Konfirmasi & Bayar</span>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </button>
                                
                                <p class="text-center text-[10px] text-gray-400 mt-4 flex justify-center gap-2">
                                    <i class="fa-solid fa-shield-halved"></i> Transaksi Aman & Terenkripsi
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- 3. JAVASCRIPT --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // FUNCTION: Custom Dropdown Logic
        function selectOption(id, nama, harga) {
            const select = document.getElementById('kamar_select');
            select.value = id;
            const event = new Event('change');
            select.dispatchEvent(event);
            document.getElementById('trigger_label').innerText = nama;
            document.getElementById('trigger_price').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(harga) + '/malam';
            document.getElementById('custom_options').classList.remove('show');
        }

        document.addEventListener('DOMContentLoaded', function() {
            
            // --- VARIABLES ---
            const checkInInput = document.getElementById('check_in');
            const checkOutInput = document.getElementById('check_out');
            const checkInContainer = document.getElementById('check_in_container');
            const checkOutContainer = document.getElementById('check_out_container');
            const checkOutBg = document.getElementById('check_out_bg');
            const checkOutIcon = document.getElementById('check_out_icon');

            // --- FLATPICKR ---
            const fpCheckIn = flatpickr(checkInInput, {
                minDate: "today",
                dateFormat: "Y-m-d",
                disableMobile: "true",
                onChange: function(selectedDates, dateStr) {
                    if (selectedDates.length > 0) {
                        const nextDay = new Date(selectedDates[0]);
                        nextDay.setDate(nextDay.getDate() + 1);
                        fpCheckOut.set('minDate', nextDay);
                        fpCheckOut.clear();
                        
                        // Aktifkan Check-Out UI
                        checkOutInput.disabled = false;
                        checkOutInput.placeholder = "Pilih Tanggal";
                        
                        checkOutContainer.classList.remove('cursor-not-allowed');
                        checkOutContainer.classList.add('cursor-pointer');
                        checkOutBg.classList.add('group-hover:bg-gray-100', 'group-hover:border-yellow-200');
                        checkOutIcon.classList.remove('bg-gray-100', 'text-gray-400');
                        checkOutIcon.classList.add('bg-yellow-100', 'text-yellow-600');

                        setTimeout(() => fpCheckOut.open(), 200); 
                    }
                    hitungTotal();
                }
            });

            const fpCheckOut = flatpickr(checkOutInput, {
                minDate: "today", 
                dateFormat: "Y-m-d",
                disableMobile: "true",
                onChange: function() { hitungTotal(); }
            });

            // --- CLICK LISTENER BUAT CONTAINER ---
            checkInContainer.addEventListener('click', function() {
                fpCheckIn.open();
            });

            checkOutContainer.addEventListener('click', function() {
                if (!checkOutInput.disabled) {
                    fpCheckOut.open();
                }
            });


            // --- CUSTOM DROPDOWN TOGGLE ---
            const trigger = document.getElementById('custom_trigger');
            const options = document.getElementById('custom_options');
            
            trigger.addEventListener('click', function(e) {
                e.stopPropagation();
                options.classList.toggle('show');
            });
            
            document.addEventListener('click', function(e) {
                if (!trigger.contains(e.target) && !options.contains(e.target)) {
                    options.classList.remove('show');
                }
            });


            // --- VARIABLES LAINNYA ---
            const kamarSelect = document.getElementById('kamar_select');
            const btnMinus = document.getElementById('btnMinus');
            const btnPlus = document.getElementById('btnPlus');
            const jumlahKamarDisplay = document.getElementById('jumlah_kamar_display');
            const jumlahKamarHidden = document.getElementById('jumlah_kamar_hidden');
            
            const previewFoto = document.getElementById('preview_foto');
            const noPreview = document.getElementById('no_preview');
            const previewTipe = document.getElementById('preview_tipe');
            const previewHarga = document.getElementById('preview_harga');
            const txtDurasi = document.getElementById('txt_durasi');
            const txtQty = document.getElementById('txt_qty');
            const txtTotal = document.getElementById('txt_total');
            const inputTotal = document.getElementById('input_total_harga');
            const btnSubmitDesktop = document.getElementById('btnSubmitDesktop');
            const btnSubmitMobile = document.getElementById('btnSubmitMobile');

            let hargaSaatIni = {{ isset($selectedRoom) ? $selectedRoom->harga : 0 }};
            let currentQty = 1; 

            // LISTENERS
            kamarSelect.addEventListener('change', updateKamar);
            
            btnMinus.addEventListener('click', function() {
                if (currentQty > 1) {
                    currentQty--;
                    updateQtyDisplay();
                    hitungTotal();
                }
            });
            btnPlus.addEventListener('click', function() {
                if (currentQty < 5) { 
                    currentQty++;
                    updateQtyDisplay();
                    hitungTotal();
                }
            });

            function updateQtyDisplay() {
                jumlahKamarDisplay.value = currentQty;
                jumlahKamarHidden.value = currentQty;
                txtQty.innerText = currentQty + " Unit";
                btnMinus.disabled = (currentQty === 1);
                btnPlus.disabled = (currentQty === 5);
            }

            function updateKamar() {
                const selectedOption = kamarSelect.options[kamarSelect.selectedIndex];
                if (kamarSelect.value) {
                    const harga = parseInt(selectedOption.getAttribute('data-harga'));
                    hargaSaatIni = harga;
                    previewFoto.src = selectedOption.getAttribute('data-foto');
                    previewFoto.classList.remove('opacity-0');
                    noPreview.classList.add('hidden');
                    previewTipe.innerText = selectedOption.getAttribute('data-tipe');
                    previewHarga.innerText = "Rp " + new Intl.NumberFormat('id-ID').format(harga);
                    hitungTotal();
                } else {
                    hargaSaatIni = 0;
                    previewFoto.classList.add('opacity-0');
                    noPreview.classList.remove('hidden');
                    previewTipe.innerText = "Pilih Kamar Dulu";
                    previewHarga.innerText = "-";
                    resetHitungan();
                }
            }

            function hitungTotal() {
                const checkInVal = checkInInput.value;
                const checkOutVal = checkOutInput.value;

                if (hargaSaatIni > 0 && checkInVal && checkOutVal) {
                    const start = new Date(checkInVal);
                    const end = new Date(checkOutVal);

                    if (end <= start) return;

                    const diffTime = Math.abs(end - start);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
                    const totalHarga = diffDays * hargaSaatIni * currentQty; 

                    txtDurasi.innerText = diffDays + " Malam";
                    txtTotal.innerText = "Rp " + new Intl.NumberFormat('id-ID').format(totalHarga);
                    inputTotal.value = totalHarga;
                    toggleButton(true);
                } else {
                    resetHitungan();
                }
            }

            function toggleButton(enable) {
                const btns = [btnSubmitDesktop, btnSubmitMobile];
                btns.forEach(btn => {
                    btn.disabled = !enable;
                    if(enable) {
                        if(btn === btnSubmitDesktop) btn.innerHTML = `<span>Konfirmasi & Bayar</span> <i class="fa-solid fa-arrow-right"></i>`;
                        else btn.innerText = "Konfirmasi & Bayar";
                    } else {
                        btn.innerText = "Lengkapi Data Dulu";
                    }
                });
            }

            function resetHitungan() {
                txtDurasi.innerText = "0 Malam";
                txtTotal.innerText = "Rp 0";
                toggleButton(false);
            }

            updateQtyDisplay();
        });
    </script>
@endsection