<x-app-layout>
    {{-- BACKGROUND HEADER (Hiasan) --}}
    <div class="bg-gray-900 h-64 w-full absolute top-0 left-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-gray-50 opacity-90"></div>
    </div>

    <div class="relative z-10 min-h-screen pt-12 pb-24">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- HEADER HALAMAN --}}
            <div class="flex flex-col md:flex-row justify-between items-end mb-8 text-white">
                <div>
                    <a href="{{ route('rooms.index') }}" class="inline-flex items-center gap-2 text-sm text-yellow-400 hover:text-yellow-300 transition mb-2 font-bold tracking-wide">
                        <i class="fa-solid fa-arrow-left"></i> KEMBALI PILIH KAMAR
                    </a>
                    <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight">Booking Confirmation</h1>
                    <p class="text-gray-300 mt-2 text-sm md:text-base">Lengkapi data di bawah ini untuk mengamankan kamar impian Anda.</p>
                </div>
                
                {{-- STEP INDICATOR (Hiasan Visual) --}}
                <div class="hidden md:flex items-center gap-2 mt-4 md:mt-0">
                    <span class="bg-yellow-500 text-gray-900 w-8 h-8 flex items-center justify-center rounded-full font-bold text-sm">1</span>
                    <div class="w-10 h-1 bg-yellow-500/50 rounded"></div>
                    <span class="bg-white text-gray-900 w-8 h-8 flex items-center justify-center rounded-full font-bold text-sm">2</span>
                    <div class="w-10 h-1 bg-gray-600 rounded"></div>
                    <span class="bg-gray-700 text-gray-400 w-8 h-8 flex items-center justify-center rounded-full font-bold text-sm">3</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- KOLOM KIRI: FORMULIR --}}
                <div class="lg:col-span-2">
                    <form action="{{ route('booking.store') }}" method="POST" id="bookingForm" class="space-y-6">
                        @csrf
                        <input type="hidden" name="total_harga" id="input_total_harga">
                        <input type="hidden" name="jumlah_kamar" value="1">

                        {{-- CARD 1: DETAIL KAMAR & TANGGAL --}}
                        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                    <i class="fa-solid fa-calendar-check"></i>
                                </div>
                                <h2 class="font-bold text-gray-800 text-lg">Jadwal & Kamar</h2>
                            </div>

                            <div class="p-6 md:p-8 space-y-6">
                                {{-- PILIH KAMAR --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Tipe Kamar</label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-400 group-hover:text-yellow-500 transition-colors">
                                            <i class="fa-solid fa-bed text-lg"></i>
                                        </div>
                                        <select name="kamar_id" id="kamar_select" required
                                            class="pl-12 w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-yellow-500 focus:border-yellow-500 block p-4 transition-all shadow-sm hover:border-yellow-400 cursor-pointer appearance-none">
                                            
                                            <option value="" data-harga="0" data-foto="">-- Pilih Tipe Kamar --</option>
                                            
                                            @foreach($rooms as $r)
                                                <option value="{{ $r->id }}" 
                                                        data-harga="{{ $r->harga }}" 
                                                        data-foto="{{ asset('storage/' . $r->foto) }}"
                                                        data-tipe="{{ $r->tipe_kamar }}"
                                                        {{ (isset($selectedRoom) && $selectedRoom->id == $r->id) ? 'selected' : '' }}>
                                                    {{ $r->tipe_kamar }} (Rp {{ number_format($r->harga, 0, ',', '.') }}/malam)
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400">
                                            <i class="fa-solid fa-chevron-down"></i>
                                        </div>
                                    </div>
                                </div>

                                {{-- PILIH TANGGAL (GRID) --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    {{-- Check In --}}
                                    <div class="relative">
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Check-In</label>
                                        <div class="relative group">
                                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-400 group-hover:text-blue-500 transition-colors">
                                                <i class="fa-regular fa-calendar"></i>
                                            </div>
                                            <input type="date" name="check_in" id="check_in" required min="{{ date('Y-m-d') }}"
                                                class="pl-12 w-full bg-blue-50/30 border border-blue-100 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-3.5 shadow-sm transition-all cursor-pointer hover:bg-white">
                                        </div>
                                    </div>

                                    {{-- Check Out --}}
                                    <div class="relative">
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Check-Out</label>
                                        <div class="relative group">
                                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-400 group-hover:text-blue-500 transition-colors">
                                                <i class="fa-regular fa-calendar-check"></i>
                                            </div>
                                            <input type="date" name="check_out" id="check_out" required min="{{ date('Y-m-d') }}"
                                                class="pl-12 w-full bg-blue-50/30 border border-blue-100 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-3.5 shadow-sm transition-all cursor-pointer hover:bg-white">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- CARD 2: DATA PEMESAN --}}
                        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600">
                                    <i class="fa-solid fa-user-shield"></i>
                                </div>
                                <h2 class="font-bold text-gray-800 text-lg">Data Pemesan</h2>
                            </div>

                            <div class="p-6 md:p-8 space-y-5">
                                {{-- Nama --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nama Lengkap</label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-400">
                                            <i class="fa-regular fa-id-card"></i>
                                        </div>
                                        <input type="text" name="nama_tamu" value="{{ Auth::user()->name }}" required
                                            class="pl-12 w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-yellow-500 focus:border-yellow-500 block p-3.5 transition-all">
                                    </div>
                                </div>

                                {{-- WhatsApp --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nomor WhatsApp</label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-400 group-hover:text-green-500 transition-colors">
                                            <i class="fa-brands fa-whatsapp text-lg"></i>
                                        </div>
                                        <input type="tel" name="nomor_hp" value="{{ Auth::user()->nomor_hp ?? '' }}" placeholder="Contoh: 0812..." required
                                            class="pl-12 w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-yellow-500 focus:border-yellow-500 block p-3.5 transition-all">
                                    </div>
                                    <p class="mt-2 text-[10px] text-gray-400 flex items-center gap-1">
                                        <i class="fa-solid fa-circle-info"></i> Bukti reservasi akan dikirim melalui WhatsApp.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- TOMBOL SUBMIT MOBILE (Muncul di bawah form di HP) --}}
                        <div class="lg:hidden">
                            <button type="submit" id="btnSubmitMobile" disabled
                                class="w-full bg-gray-300 text-white font-bold py-4 rounded-xl shadow-lg cursor-not-allowed transition-all">
                                Lengkapi Data Dulu
                            </button>
                        </div>

                    </form>
                </div>

                {{-- KOLOM KANAN: RINGKASAN (STICKY) --}}
                <div class="lg:col-span-1">
                    <div class="sticky top-6">
                        <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden transform transition-all hover:scale-[1.02] duration-300">
                            
                            {{-- FOTO KAMAR (Dynamic) --}}
                            <div class="h-48 overflow-hidden relative bg-gray-900">
                                {{-- Placeholder --}}
                                <div id="no_preview" class="absolute inset-0 flex flex-col items-center justify-center text-gray-500 {{ isset($selectedRoom) ? 'hidden' : '' }}">
                                    <i class="fa-solid fa-hotel text-4xl mb-2 opacity-50"></i>
                                    <span class="text-xs uppercase tracking-widest">Pilih Kamar</span>
                                </div>
                                
                                {{-- Image Preview --}}
                                <img id="preview_foto" src="{{ isset($selectedRoom) ? asset('storage/' . $selectedRoom->foto) : '' }}" 
                                    class="w-full h-full object-cover transition-opacity duration-700 {{ isset($selectedRoom) ? 'opacity-100' : 'opacity-0' }}">
                                
                                {{-- Gradient Overlay --}}
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                                
                                {{-- Tipe Kamar Label --}}
                                <div class="absolute bottom-4 left-4 right-4">
                                    <span class="bg-yellow-500 text-gray-900 text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider mb-1 inline-block shadow-sm">Selected Room</span>
                                    <h3 id="preview_tipe" class="font-extrabold text-white text-xl shadow-sm truncate">
                                        {{ isset($selectedRoom) ? $selectedRoom->tipe_kamar : 'Belum Dipilih' }}
                                    </h3>
                                </div>
                            </div>

                            {{-- DETAIL HARGA --}}
                            <div class="p-6 relative">
                                {{-- Pattern Hiasan --}}
                                <div class="absolute top-0 right-0 p-4 opacity-5 pointer-events-none">
                                    <i class="fa-solid fa-receipt text-6xl"></i>
                                </div>

                                <div class="space-y-4">
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-500">Harga per malam</span>
                                        <span class="font-bold text-gray-900" id="preview_harga">
                                            {{ isset($selectedRoom) ? 'Rp ' . number_format($selectedRoom->harga, 0, ',', '.') : '-' }}
                                        </span>
                                    </div>
                                    
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-500">Durasi Menginap</span>
                                        <span class="font-bold text-gray-900 bg-gray-100 px-2 py-0.5 rounded" id="txt_durasi">0 Malam</span>
                                    </div>

                                    <div class="border-t border-dashed border-gray-300 my-4"></div>

                                    <div class="flex flex-col gap-1">
                                        <span class="text-xs text-gray-500 uppercase font-bold tracking-wider">Total Pembayaran</span>
                                        <span class="text-3xl font-extrabold text-gray-900" id="txt_total">Rp 0</span>
                                        <span class="text-[10px] text-green-600 font-bold flex items-center gap-1">
                                            <i class="fa-solid fa-check-circle"></i> Termasuk pajak & layanan
                                        </span>
                                    </div>
                                </div>

                                {{-- TOMBOL SUBMIT DESKTOP --}}
                                <div class="mt-8 hidden lg:block">
                                    <button type="submit" form="bookingForm" id="btnSubmitDesktop" disabled
                                        class="w-full bg-gray-300 text-white font-bold py-4 rounded-xl shadow-lg cursor-not-allowed transition-all flex justify-center items-center gap-2 group">
                                        <span>Lengkapi Data</span>
                                        <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Security Badge --}}
                        <div class="mt-6 text-center">
                            <p class="text-xs text-gray-400 flex justify-center items-center gap-2">
                                <i class="fa-solid fa-lock"></i> Transaksi Anda Dijamin Aman 100%
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- SCRIPT --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elemen HTML
            const kamarSelect = document.getElementById('kamar_select');
            const checkInInput = document.getElementById('check_in');
            const checkOutInput = document.getElementById('check_out');
            
            const previewFoto = document.getElementById('preview_foto');
            const noPreview = document.getElementById('no_preview');
            const previewTipe = document.getElementById('preview_tipe');
            const previewHarga = document.getElementById('preview_harga');
            
            const txtDurasi = document.getElementById('txt_durasi');
            const txtTotal = document.getElementById('txt_total');
            const inputTotal = document.getElementById('input_total_harga');
            
            const btnSubmitDesktop = document.getElementById('btnSubmitDesktop');
            const btnSubmitMobile = document.getElementById('btnSubmitMobile');

            let hargaSaatIni = {{ isset($selectedRoom) ? $selectedRoom->harga : 0 }};

            // 1. GANTI KAMAR
            kamarSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                
                if (this.value) {
                    const harga = parseInt(selectedOption.getAttribute('data-harga'));
                    const foto = selectedOption.getAttribute('data-foto');
                    const tipe = selectedOption.getAttribute('data-tipe');

                    hargaSaatIni = harga;

                    previewFoto.src = foto;
                    previewFoto.classList.remove('opacity-0');
                    noPreview.classList.add('hidden');
                    
                    previewTipe.innerText = tipe;
                    previewHarga.innerText = "Rp " + new Intl.NumberFormat('id-ID').format(harga);

                    hitungTotal();
                } else {
                    hargaSaatIni = 0;
                    previewFoto.classList.add('opacity-0');
                    noPreview.classList.remove('hidden');
                    previewTipe.innerText = "Belum Dipilih";
                    previewHarga.innerText = "-";
                    resetHitungan();
                }
            });

            // 2. HITUNG TOTAL
            function hitungTotal() {
                if (hargaSaatIni > 0 && checkInInput.value && checkOutInput.value) {
                    const start = new Date(checkInInput.value);
                    const end = new Date(checkOutInput.value);

                    if (end <= start) {
                        tampilkanError("Tanggal Salah");
                        return;
                    }

                    const diffTime = Math.abs(end - start);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 

                    const totalHarga = diffDays * hargaSaatIni;

                    txtDurasi.innerText = diffDays + " Malam";
                    txtTotal.innerText = "Rp " + new Intl.NumberFormat('id-ID').format(totalHarga);

                    inputTotal.value = totalHarga;
                    aktifkanTombol(true);
                } else {
                    resetHitungan();
                }
            }

            function aktifkanTombol(aktif) {
                const btns = [btnSubmitDesktop, btnSubmitMobile];
                btns.forEach(btn => {
                    if(aktif) {
                        btn.disabled = false;
                        btn.innerHTML = `<span>Konfirmasi & Bayar</span> <i class="fa-solid fa-check ml-2"></i>`;
                        btn.className = "w-full bg-yellow-500 text-gray-900 font-bold py-4 rounded-xl shadow-xl hover:bg-yellow-400 transition-all flex justify-center items-center gap-2 transform hover:-translate-y-1 cursor-pointer";
                    } else {
                        btn.disabled = true;
                        btn.innerText = "Lengkapi Data";
                        btn.className = "w-full bg-gray-300 text-white font-bold py-4 rounded-xl shadow-lg cursor-not-allowed transition-all flex justify-center items-center gap-2";
                    }
                });
            }

            function resetHitungan() {
                txtDurasi.innerText = "0 Malam";
                txtTotal.innerText = "Rp 0";
                aktifkanTombol(false);
            }

            function tampilkanError(msg) {
                txtDurasi.innerText = msg;
                txtTotal.innerText = "-";
                aktifkanTombol(false);
            }

            checkInInput.addEventListener('change', hitungTotal);
            checkOutInput.addEventListener('change', hitungTotal);
        });
    </script>
</x-app-layout>