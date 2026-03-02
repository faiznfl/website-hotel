<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Kamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Midtrans\Config; 
use Midtrans\Snap;   

class BookingController extends Controller
{
    // --- HELPER: Function Khusus Stok ---
    private function getTotalStok($tipeKamar)
    {
        $tipe = strtolower($tipeKamar);
        if (str_contains($tipe, 'superior')) return 9;
        if (str_contains($tipe, 'deluxe')) return 14;
        if (str_contains($tipe, 'family')) return 2;
        return 5; 
    }

    // =================================================================
    // 1. HALAMAN FORM BOOKING
    // =================================================================
    public function create(Request $request)
    {
        $kamarId = $request->query('room_id');
        $rooms = Kamar::all();
        $selectedRoom = $kamarId ? Kamar::find($kamarId) : null;

        return view('booking', compact('rooms', 'selectedRoom'));
    }

    // =================================================================
    // 2. PROSES SIMPAN BOOKING & MINTA TOKEN MIDTRANS
    // =================================================================
    public function store(Request $request)
    {
        $request->validate([
            'nama_tamu' => 'required|string|max:255',
            'nomor_hp' => 'required|string',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'jumlah_kamar' => 'required|integer|min:1',
            'kamar_id' => 'required|exists:kamars,id',
            'total_harga' => 'required|numeric',
        ]);

        $kamar = Kamar::find($request->kamar_id);
        
        // Simpan Booking dengan waktu expired (24 jam)
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
            'expires_at'   => now()->addHours(24), // Set expired di DB
        ]);

        try {
            Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            Config::$isProduction = false; 
            Config::$isSanitized = true;
            Config::$is3ds = true;

            $params = [
                'transaction_details' => [
                    'order_id' => 'BOOKING-' . $booking->id . '-' . time(),
                    'gross_amount' => (int) $booking->total_harga,
                ],
                // TAMBAHKAN INI: Sinkronisasi Expired ke Midtrans
                'expiry' => [
                    'start_time' => date("Y-m-d H:i:s O", strtotime($booking->created_at)),
                    'unit' => 'hour',
                    'duration' => 24
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

            $snapToken = Snap::getSnapToken($params);
            $booking->snap_token = $snapToken;
            $booking->save();

            return redirect()->route('booking.payment', $booking->id);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal terhubung ke Midtrans: ' . $e->getMessage());
        }
    }

    // =================================================================
    // 3. HALAMAN PEMBAYARAN
    // =================================================================
    public function payment($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id !== Auth::id()) { abort(403); }

        if ($booking->status == 'confirmed') {
            return redirect()->route('booking.history')->with('success', 'Booking ini sudah lunas.');
        }

        return view('payment', compact('booking'));
    }

    // =================================================================
    // 4. FITUR RIWAYAT & DETAIL (USER SIDE)
    // =================================================================

    public function history()
    {
        $sekarang = Carbon::now('Asia/Jakarta');

        // 1. Eksekusi pembatalan instan sebelum data ditarik
        Booking::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->where('expires_at', '<', $sekarang)
            ->update(['status' => 'cancelled']);

        // 2. Ambil data terbaru
        $bookings = Booking::with('kamar')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();
                
        return view('user.history', compact('bookings'));
    }

    public function show($id)
    {
        $booking = Booking::with('kamar')->findOrFail($id);

        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        // Proteksi: Tidak bisa lihat invoice jika belum bayar
        if ($booking->status == 'pending') {
            return redirect()->route('booking.history')
                ->with('error', 'Selesaikan pembayaran terlebih dahulu untuk melihat detail reservasi.');
        }

        return view('user.invoice', compact('booking'));
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
}