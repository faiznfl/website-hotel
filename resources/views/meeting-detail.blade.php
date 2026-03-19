@extends('layouts.main')

@section('title', $meeting->judul . ' - Hotel Rumah RB')

@section('content')

    {{-- HEADER IMAGE --}}
    {{-- TWEAK: Tinggi di HP disesuaikan (50vh) agar tidak terlalu panjang --}}
    <div class="relative h-[50vh] md:h-[60vh] w-full overflow-hidden">
        <img src="{{ asset('storage/' . $meeting->gambar) }}" alt="{{ $meeting->judul }}"
            class="w-full h-full object-cover object-center">

        <div class="absolute inset-0 bg-black/50"></div>

        {{-- Judul di Kiri Bawah --}}
        {{-- TWEAK: Padding di HP diperkecil (p-6) --}}
        <div class="absolute bottom-0 left-0 w-full p-6 md:p-12 bg-gradient-to-t from-black/90 to-transparent">
            <div class="max-w-screen-xl mx-auto">
                {{-- TWEAK: Font Size di HP (text-2xl) agar tidak tumpuk --}}
                <h1
                    class="text-2xl sm:text-3xl md:text-5xl font-extrabold text-white uppercase tracking-wide mb-2 shadow-sm leading-tight">
                    {{ $meeting->judul }}
                </h1>
            </div>
        </div>
    </div>

    {{-- KONTEN UTAMA --}}
    <section class="py-8 md:py-12 bg-gray-50 min-h-screen">
        <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-10">

                {{-- KOLOM KIRI (Info) --}}
                <div class="lg:col-span-2 space-y-6 md:space-y-8">

                    {{-- Deskripsi --}}
                    <div class="bg-white rounded-2xl p-5 md:p-8 shadow-sm border border-gray-100">
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-4 border-l-4 border-yellow-500 pl-4">
                            ABOUT THIS VENUE
                        </h3>
                        <div class="text-gray-600 text-sm md:text-base leading-relaxed text-justify prose max-w-none">
                            {!! $meeting->deskripsi !!}
                        </div>
                    </div>

                    {{-- Fasilitas --}}
                    <div class="bg-white rounded-2xl p-5 md:p-8 shadow-sm border border-gray-100">
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-6 border-l-4 border-yellow-500 pl-4">
                            INCLUDED FACILITIES
                        </h3>
                        @if ($meeting->fasilitas)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                                @foreach (explode(',', $meeting->fasilitas) as $facility)
                                    <div
                                        class="flex items-center gap-3 p-3 md:p-4 border border-gray-100 rounded-xl bg-gray-50 hover:bg-yellow-50 transition-colors">
                                        <div
                                            class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-yellow-500 shadow-sm flex-shrink-0">
                                            <i class="fa-solid fa-check"></i>
                                        </div>
                                        <span class="text-gray-700 text-sm md:text-base font-medium">{{ trim($facility) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-400 italic text-sm">Hubungi kami untuk detail fasilitas.</p>
                        @endif
                    </div>

                </div>

                {{-- KOLOM KANAN (Booking Form) --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 sticky top-20">

                        <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                            <i class="fa-solid fa-calendar-check text-yellow-500"></i> BOOK THIS VENUE
                        </h3>

                        @if(session('success'))
                            <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-r text-sm animate-pulse">
                                {{ session('success') }}
                            </div>
                        @endif

                        {{-- Cek Login: Hanya user login yang bisa isi form --}}
                        @auth
                            <form action="{{ route('meeting.reserve', $meeting->id) }}" method="POST" class="space-y-4">
                                @csrf

                                {{-- Pesan Error Validasi --}}
                                @if ($errors->any())
                                    <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-xs rounded-r-lg">
                                        <ul class="list-disc pl-4">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                {{-- Input Tanggal --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wider">Event Date</label>
                                    <input type="date" name="tanggal_booking" value="{{ old('tanggal_booking') }}" min="{{ date('Y-m-d') }}"
                                        required
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-yellow-400 focus:outline-none focus:bg-white text-sm transition-all">
                                </div>

                                {{-- Input Jam --}}
                                <div class="grid grid-cols-2 gap-4">
                                    {{-- Start Time --}}
                                    <div class="relative">
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Start</label>
                                        <div class="relative group">
                                            <span
                                                class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 group-focus-within:text-yellow-500">
                                                <i class="fa-regular fa-clock"></i>
                                            </span>
                                            <input type="text" id="start_time" name="jam_mulai" value="{{ old('jam_mulai') }}" required
                                                placeholder="09:00"
                                                class="timepicker w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-yellow-400 focus:outline-none text-sm">
                                        </div>
                                    </div>

                                    {{-- End Time --}}
                                    <div class="relative">
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">End</label>
                                        <div class="relative group">
                                            <span
                                                class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 group-focus-within:text-yellow-500">
                                                <i class="fa-regular fa-clock"></i>
                                            </span>
                                            <input type="text" id="end_time" name="jam_selesai" value="{{ old('jam_selesai') }}" required
                                                placeholder="17:00"
                                                class="timepicker w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-yellow-400 focus:outline-none text-sm">
                                        </div>
                                    </div>
                                </div>

                                {{-- BOX TOTAL DURASI --}}
                                <div id="duration_wrapper" class="mt-4 hidden" style="display: none;">
                                    <div class="flex items-center justify-between p-4 bg-gray-900 rounded-2xl border border-gray-800 shadow-lg">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-yellow-500/20 flex items-center justify-center text-yellow-500">
                                                <i class="fa-solid fa-hourglass-half text-lg"></i>
                                            </div>
                                            <div>
                                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Estimasi Durasi</p>
                                                <p id="total_duration_text" class="text-sm font-bold text-white">0 Jam</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span
                                                class="text-[10px] px-2 py-1 bg-green-500/20 text-green-400 rounded-full font-bold uppercase">Ready</span>
                                        </div>
                                    </div>
                                </div>

                                <script>
                                    document.addEventListener('DOMContentLoaded', function () {
                                        // Fungsi Hitung Durasi
                                        function updateDuration() {
                                            const startStr = document.getElementById('start_time').value;
                                            const endStr = document.getElementById('end_time').value;
                                            const wrapper = document.getElementById('duration_wrapper');
                                            const text = document.getElementById('total_duration_text');

                                            if (startStr && endStr) {
                                                const start = startStr.split(':');
                                                const end = endStr.split(':');

                                                const startDate = new Date(0, 0, 0, start[0], start[1], 0);
                                                const endDate = new Date(0, 0, 0, end[0], end[1], 0);

                                                let diff = endDate - startDate;

                                                // Jika jam selesai di hari berikutnya (misal mulai 23:00 selesai 01:00)
                                                if (diff < 0) {
                                                    diff += 86400000; // tambah 24 jam dalam milidetik
                                                }

                                                const hours = Math.floor(diff / 1000 / 60 / 60);
                                                const minutes = Math.floor((diff / 1000 / 60) % 60);

                                                let result = "";
                                                if (hours > 0) result += hours + " Jam ";
                                                if (minutes > 0) result += minutes + " Menit";
                                                if (hours === 0 && minutes === 0) result = "0 Menit";

                                                text.innerText = result;
                                                wrapper.style.display = 'block'; // Paksa muncul pakai style inline
                                                wrapper.classList.remove('hidden');
                                            }
                                        }

                                        // Konfigurasi Flatpickr
                                        const fpConfig = {
                                            enableTime: true,
                                            noCalendar: true,
                                            dateFormat: "H:i",
                                            time_24hr: true,
                                            disableMobile: "true",
                                            onClose: function (selectedDates, dateStr, instance) {
                                                updateDuration(); // Hitung saat popup ditutup
                                            },
                                            onChange: function (selectedDates, dateStr, instance) {
                                                updateDuration(); // Hitung saat nilai berubah
                                            }
                                        };

                                        // Inisialisasi ke ID masing-masing
                                        flatpickr("#start_time", fpConfig);
                                        flatpickr("#end_time", fpConfig);
                                    });
                                </script>

                                <button type="submit"
                                    class="w-full bg-gray-900 text-white font-bold py-4 rounded-xl hover:bg-yellow-500 transition-all shadow-lg shadow-gray-200 hover:shadow-yellow-200 transform active:scale-95 uppercase tracking-widest text-sm">
                                    SUBMIT RESERVATION
                                </button>
                            </form>
                        @else
                            <div class="bg-gray-50 p-6 rounded-2xl border border-dashed border-gray-300 text-center">
                                <p class="text-sm text-gray-500 mb-4">Please login to make a reservation.</p>
                                <a href="/login"
                                    class="inline-block bg-yellow-500 text-white px-6 py-2 rounded-full font-bold text-sm">Login Now</a>
                            </div>
                        @endauth

                        <div class="relative my-8">
                            <div class="absolute inset-0 flex items-center"><span class="w-full border-t border-gray-100"></span></div>
                            <div class="relative flex justify-center text-xs uppercase"><span class="bg-white px-2 text-gray-400">Or
                                    Quick Inquiry</span></div>
                        </div>

                        {{-- Tetap sediakan tombol WA untuk tanya-tanya --}}
                        <a href="https://wa.me/6281363374155?text=Halo%20Admin,%20saya%20ingin%20tanya%20tentang%20ruangan%20{{ urlencode($meeting->judul) }}"
                            target="_blank"
                            class="flex justify-center items-center gap-2 w-full text-green-600 font-bold py-3 rounded-xl border-2 border-green-600 hover:bg-green-50 transition-all text-sm">
                            <i class="fa-brands fa-whatsapp text-lg"></i> Chat via WhatsApp
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection