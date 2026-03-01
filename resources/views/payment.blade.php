@extends('layouts.main')

@section('title', 'Pembayaran - Hotel Rumah RB')

@section('content')
    {{-- LIBRARY MIDTRANS (Wajib Ada) --}}
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>

    <div class="min-h-screen bg-gray-50 pt-32 pb-20 px-4">
        <div class="max-w-xl mx-auto">

            {{-- HEADER --}}
            <div class="text-center mb-8">
                <div
                    class="w-16 h-16 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl animate-pulse">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <h1 class="text-3xl font-extrabold text-gray-900">Selesaikan Pembayaran</h1>
                <p class="text-gray-500 mt-2">Pesanan #{{ $booking->kode_booking ?? $booking->id }} menunggu pembayaran.</p>
            </div>

            {{-- CARD INVOICE --}}
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">

                {{-- Detail Kamar --}}
                <div class="p-8 border-b border-gray-100">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Tipe Kamar</p>
                            <h2 class="text-xl font-bold text-gray-900">{{ $booking->kamar->tipe_kamar }}</h2>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ \Carbon\Carbon::parse($booking->check_in)->format('d M Y') }}
                                <span class="mx-1 text-gray-300">➜</span>
                                {{ \Carbon\Carbon::parse($booking->check_out)->format('d M Y') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span
                                class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wide">
                                {{ $booking->status }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Rincian Biaya --}}
                <div class="p-8 bg-gray-50/50">
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Nama Tamu</span>
                            <span class="font-semibold text-gray-900">{{ $booking->nama_tamu }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">No. Handphone</span>
                            <span class="font-semibold text-gray-900">{{ $booking->nomor_hp }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Durasi</span>
                            <span class="font-semibold text-gray-900">
                                {{ \Carbon\Carbon::parse($booking->check_in)->diffInDays(\Carbon\Carbon::parse($booking->check_out)) }}
                                Malam
                            </span>
                        </div>
                    </div>

                    <div class="border-t border-dashed border-gray-300 my-6"></div>

                    <div class="flex justify-between items-end">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Total Tagihan</p>
                            <p class="text-3xl font-black text-gray-900 tracking-tight">
                                Rp {{ number_format($booking->total_harga, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="p-6 bg-white border-t border-gray-100">
                    <button id="pay-button"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-blue-600/30 transition-all transform hover:-translate-y-1 flex justify-center items-center gap-2">
                        <span>Bayar Sekarang</span>
                        <i class="fa-solid fa-lock text-sm opacity-70"></i>
                    </button>

                    {{-- Pastikan route 'booking.history' sudah ada, kalau belum ganti jadi '/' (Home) --}}
                    <a href="{{ Route::has('booking.history') ? route('booking.history') : '/' }}"
                        class="block text-center text-sm text-gray-400 font-medium mt-4 hover:text-gray-600">
                        Bayar Nanti (Simpan di Riwayat)
                    </a>
                </div>
            </div>

            <div class="text-center mt-8 text-gray-400 text-sm">
                <i class="fa-solid fa-shield-halved mr-1"></i> Pembayaran diamankan oleh Midtrans
            </div>
        </div>
    </div>

    {{-- SCRIPT MIDTRANS --}}
    <script type="text/javascript">
        var payButton = document.getElementById('pay-button');

        payButton.addEventListener('click', function () {
            // Trigger Snap Popup dengan Token dari Database
            window.snap.pay('{{ $booking->snap_token }}', {
                onSuccess: function (result) {
                    // Redirect ke Halaman Sukses / History
                    // Cek apakah route booking.history ada, jika tidak ke home
                    window.location.href = "{{ Route::has('booking.history') ? route('booking.history') : '/' }}?status=success";
                },
                onPending: function (result) {
                    window.location.href = "{{ Route::has('booking.history') ? route('booking.history') : '/' }}?status=pending";
                },
                onError: function (result) {
                    alert("Pembayaran Gagal! Silakan coba lagi.");
                },
                onClose: function () {
                    alert('Anda belum menyelesaikan pembayaran.');
                }
            });
        });
    </script>
@endsection