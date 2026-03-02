@extends('layouts.main')

@section('title', 'Pembayaran - Hotel Rumah RB')

@section('content')
    {{-- LIBRARY MIDTRANS --}}
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>

    <div class="bg-gray-50 min-h-screen py-12 pt-32">
        <div class="max-w-4xl mx-auto px-4">

            {{-- HEADER SIMPLE --}}
            <div class="mb-8">
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Selesaikan Pembayaran</h1>
                <p class="text-gray-500 mt-1">Silakan periksa kembali rincian pesanan Anda sebelum membayar.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- KIRI: RINCIAN (Sama gayanya dengan Card History) --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 relative overflow-hidden">
                        {{-- Garis Status (Aksen Kuning sesuai history) --}}
                        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-yellow-500"></div>

                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-bold text-gray-900">Detail Reservasi</h3>
                            <span
                                class="text-[10px] bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full font-bold uppercase tracking-wider">
                                Menunggu Pembayaran
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-[10px] uppercase text-gray-400 font-bold tracking-widest mb-1">Tipe Kamar</p>
                                <p class="font-bold text-gray-800 text-lg">{{ $booking->kamar->tipe_kamar }}</p>
                                <p class="text-sm text-gray-500">{{ $booking->jumlah_kamar }} Kamar</p>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase text-gray-400 font-bold tracking-widest mb-1">Nama Tamu</p>
                                <p class="font-bold text-gray-800 text-lg">{{ $booking->nama_tamu }}</p>
                                <p class="text-sm text-gray-500">{{ $booking->nomor_hp }}</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-[10px] uppercase text-gray-400 font-bold tracking-widest mb-1">Jadwal
                                    Menginap</p>
                                <div class="flex items-center gap-3">
                                    <p class="font-bold text-gray-800">
                                        {{ \Carbon\Carbon::parse($booking->check_in)->format('d M Y') }}</p>
                                    <i class="fa-solid fa-arrow-right text-gray-300 text-xs"></i>
                                    <p class="font-bold text-gray-800">
                                        {{ \Carbon\Carbon::parse($booking->check_out)->format('d M Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Info Keamanan --}}
                    <div class="flex items-center gap-3 text-gray-400 px-4">
                        <i class="fa-solid fa-shield-halved text-lg"></i>
                        <p class="text-xs font-medium">Pembayaran aman dan terenkripsi melalui sistem Midtrans Snap.</p>
                    </div>
                </div>

                {{-- KANAN: TOTAL & TIMER --}}
                <div class="space-y-6">
                    {{-- Timer Box --}}
                    <div class="bg-gray-900 rounded-3xl p-6 text-white text-center shadow-lg shadow-gray-200">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Batas Waktu Bayar</p>
                        <div id="payment-timer" data-expire="{{ $booking->expires_at }}"
                            class="text-2xl font-mono font-black tracking-tighter">
                            00 : 00 : 00
                        </div>
                    </div>

                    {{-- Billing Box --}}
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-sm text-gray-500">Total Tagihan</span>
                        </div>
                        <div class="text-3xl font-black text-gray-900 mb-8">
                            <span class="text-base font-bold text-gray-300">Rp</span>
                            {{ number_format($booking->total_harga, 0, ',', '.') }}
                        </div>

                        <button id="pay-button"
                            class="w-full bg-gray-900 hover:bg-yellow-500 hover:text-black text-white font-bold py-4 rounded-2xl transition-all shadow-lg active:scale-95 flex items-center justify-center gap-2">
                            <span>Bayar Sekarang</span>
                            <i class="fa-solid fa-credit-card text-sm"></i>
                        </button>

                        <a href="{{ route('booking.history') }}"
                            class="block text-center text-xs font-bold text-gray-400 hover:text-gray-900 mt-6 transition-colors uppercase tracking-widest">
                            Kembali ke Riwayat
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- SCRIPT SINKRON --}}
    <script type="text/javascript">
        // Timer Logic
        const timerElement = document.getElementById('payment-timer');
        if (timerElement) {
            const expireDate = new Date(timerElement.getAttribute('data-expire')).getTime();
            const x = setInterval(function () {
                const now = new Date().getTime();
                const distance = expireDate - now;
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                if (distance < 0) {
                    clearInterval(x);
                    timerElement.innerHTML = "WAKTU HABIS";
                    window.location.reload();
                } else {
                    timerElement.innerHTML =
                        (hours < 10 ? "0" + hours : hours) + " : " +
                        (minutes < 10 ? "0" + minutes : minutes) + " : " +
                        (seconds < 10 ? "0" + seconds : seconds);
                }
            }, 1000);
        }

        // Midtrans Snap
        document.getElementById('pay-button').addEventListener('click', function () {
            window.snap.pay('{{ $booking->snap_token }}', {
                onSuccess: function (result) { window.location.href = "{{ route('booking.history') }}?status=success"; },
                onPending: function (result) { window.location.href = "{{ route('booking.history') }}?status=pending"; },
                onError: function (result) { alert("Pembayaran gagal."); },
                onClose: function () { console.log('Customer closed the popup'); }
            });
        });
    </script>
@endsection