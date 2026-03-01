<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Kamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Midtrans\Config; // PENTING: Import Midtrans
use Midtrans\Snap;   // PENTING: Import Midtrans

class BookingController extends Controller
{
    // --- HELPER: Function Khusus Stok (Hardcode sesuai request kamu) ---
    private function getTotalStok($tipeKamar)
    {
        $tipe = strtolower($tipeKamar);
        
        if (str_contains($tipe, 'superior')) return 9;
        if (str_contains($tipe, 'deluxe')) return 14;
        if (str_contains($tipe, 'family')) return 2;
        
        return 5; // Default
    }

    // =================================================================
    // 1. HALAMAN FORM BOOKING
    // =================================================================
    public function create(Request $request)
    {
        // 1. Ambil ID Kamar dari URL (?room_id=1)
        $kamarId = $request->query('room_id');

        // 2. Ambil SEMUA data kamar (Untuk Dropdown)
        $rooms = Kamar::all();

        // 3. Cek apakah user sudah memilih kamar sebelumnya
        $selectedRoom = null;
        if ($kamarId) {
            $selectedRoom = Kamar::find($kamarId);
        }

        return view('booking', compact('rooms', 'selectedRoom'));
    }

    // =================================================================
    // 2. PROSES SIMPAN BOOKING & MINTA TOKEN MIDTRANS
    // =================================================================
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'nama_tamu' => 'required|string|max:255',
            'nomor_hp' => 'required|string',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'jumlah_kamar' => 'required|integer|min:1',
            'kamar_id' => 'required|exists:kamars,id',
            'total_harga' => 'required|numeric',
        ]);

        // 2. Cek Stok (Gunakan logika Anda yang sudah ada)
        $kamar = Kamar::find($request->kamar_id);
        
        // 3. Simpan ke Database Dulu (Status: pending)
        $booking = Booking::create([
            'user_id'      => Auth::id(),
            'kamar_id'     => $request->kamar_id,
            'nama_tamu'    => $request->nama_tamu,
            'nomor_hp'     => $request->nomor_hp,
            'check_in'     => $request->check_in,
            'check_out'    => $request->check_out,
            'jumlah_kamar' => $request->jumlah_kamar,
            'total_harga'  => $request->total_harga,
            'status'       => 'pending',
        ]);

        // 4. INTEGRASI MIDTRANS
        try {
            // Konfigurasi Langsung dari ENV agar lebih aman saat testing
            Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            Config::$isProduction = false; // Paksa false untuk Sandbox
            Config::$isSanitized = true;
            Config::$is3ds = true;

            $params = [
                'transaction_details' => [
                    'order_id' => 'BOOKING-' . $booking->id . '-' . time(),
                    'gross_amount' => (int) $booking->total_harga,
                ],
                'customer_details' => [
                    'first_name' => $booking->nama_tamu,
                    'email' => Auth::user()->email,
                    'phone' => $booking->nomor_hp,
                ],
                'item_details' => [
                    [
                        'id' => $kamar->id,
                        'price' => (int) ($booking->total_harga / $booking->jumlah_kamar),
                        'quantity' => (int) $booking->jumlah_kamar,
                        'name' => substr("Sewa " . $kamar->tipe_kamar, 0, 50)
                    ]
                ]
            ];

            // Minta token ke Midtrans
            $snapToken = Snap::getSnapToken($params);
            
            // UPDATE TOKEN KE DATABASE
            $booking->snap_token = $snapToken;
            $booking->save();

            // Redirect ke halaman pembayaran yang ada tombol bayarnya
            return redirect()->route('booking.payment', $booking->id);

        } catch (\Exception $e) {
            // JIKA ERROR, TAMPILKAN PESANNYA DI LAYAR (Penting untuk Debugging!)
            dd("Gagal mengambil token Midtrans: " . $e->getMessage());
        }
    }

    // =================================================================
    // 3. HALAMAN PEMBAYARAN (TAMPILKAN TOMBOL BAYAR)
    // =================================================================
    public function payment($id)
    {
        $booking = Booking::findOrFail($id);

        // Pastikan yang akses adalah pemilik booking
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        // Jika sudah lunas, jangan bayar lagi
        if ($booking->status == 'confirmed') {
            return redirect()->route('booking.history')->with('success', 'Booking ini sudah lunas.');
        }

        return view('payment', compact('booking'));
    }

    // =================================================================
    // 4. FITUR LAINNYA
    // =================================================================

    public function history()
    {
        $bookings = Booking::with('kamar')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();
            
        return view('user.history', compact('bookings'));
    }

    public function cancel($id)
    {
        $booking = Booking::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        if ($booking->status == 'pending') {
            $booking->status = 'cancelled';
            $booking->save();
            return redirect()->back()->with('success', 'Pesanan berhasil dibatalkan.');
        }

        return redirect()->back()->with('error', 'Pesanan tidak dapat dibatalkan.');
    }

    public function show($id)
    {
        $booking = Booking::with('kamar')->findOrFail($id);

        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('user.invoice', compact('booking'));
    }
}