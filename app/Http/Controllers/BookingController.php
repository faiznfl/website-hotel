<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Kamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class BookingController extends Controller
{
    // --- HELPER: Function Khusus Stok ---
    private function getTotalStok($tipeKamar)
    {
        $tipe = strtolower($tipeKamar);
        
        if (str_contains($tipe, 'superior')) return 9;
        if (str_contains($tipe, 'deluxe')) return 14;
        if (str_contains($tipe, 'family')) return 2;
        
        return 5; // Default
    }

    // =================================================================
    // 1. FUNGSI BARU: MENAMPILKAN HALAMAN BOOKING (View)
    // =================================================================
    public function create(Request $request)
    {
        // 1. Ambil ID Kamar (Kalau ada)
        $kamarId = $request->query('room_id');

        // 2. Ambil SEMUA data kamar (Untuk Dropdown)
        $rooms = \App\Models\Kamar::all();

        // 3. Cek apakah user sudah memilih kamar sebelumnya
        $selectedRoom = null;
        if ($kamarId) {
            $selectedRoom = \App\Models\Kamar::find($kamarId);
        }

        // 4. Tampilkan View
        return view('booking', compact('rooms', 'selectedRoom'));
    }

    // =================================================================
    // 2. FUNGSI SIMPAN BOOKING (Store)
    // =================================================================
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'nama_tamu' => 'required|string|max:255',
            'nomor_hp' => 'required|string',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'jumlah_kamar' => 'required|integer|min:1',
            'kamar_id' => 'required|exists:kamars,id', // Wajib ada ID Kamar
            'total_harga' => 'required|numeric', // Wajib ada Total Harga
        ]);

        // 2. Ambil ID Kamar
        $kamarId = $request->kamar_id;

        // 3. VALIDASI STOK
        $kamar = Kamar::find($kamarId);
        $totalStokKamar = $this->getTotalStok($kamar->tipe_kamar);

        // Cek Stok Fisik
        if ($request->jumlah_kamar > $totalStokKamar) {
            return back()->with('error', "Maaf, tipe {$kamar->tipe_kamar} hanya memiliki total {$totalStokKamar} kamar.");
        }

        // Cek Ketersediaan Tanggal (Supaya tidak double book)
        $kamarTerpakai = Booking::where('kamar_id', $kamarId)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($request) {
                $query->where('check_in', '<', $request->check_out)
                      ->where('check_out', '>', $request->check_in);
            })
            ->sum('jumlah_kamar');

        $sisaKamar = $totalStokKamar - $kamarTerpakai;

        if ($request->jumlah_kamar > $sisaKamar) {
            return back()->with('error', "Maaf, pada tanggal tersebut sisa kamar {$kamar->tipe_kamar} hanya tinggal {$sisaKamar} unit.");
        }

        // 4. Simpan ke Database
        $booking = Booking::create([
            'user_id'      => Auth::id(),
            'kamar_id'     => $kamarId,
            'nama_tamu'    => $request->nama_tamu,
            'nomor_hp'     => $request->nomor_hp,
            'check_in'     => $request->check_in,
            'check_out'    => $request->check_out,
            'jumlah_kamar' => $request->jumlah_kamar,
            'total_harga'  => $request->total_harga, // PENTING: Simpan harga
            'status'       => 'pending',
        ]);

        return redirect()->route('booking.history')->with('success', "Reservasi Berhasil! Kode Booking: {$booking->kode_booking}.");
    }

    // --- 3. Fungsi Lihat Riwayat (History) ---
    public function history()
    {
        $bookings = Booking::with('kamar')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();
            
        return view('user.history', compact('bookings'));
    }

    // --- 4. Fungsi Batalkan Pesanan (Cancel) ---
    public function cancel($id)
    {
        $booking = Booking::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($booking->status == 'pending') {
            $booking->status = 'cancelled';
            $booking->save();
            return redirect()->back()->with('success', 'Pesanan berhasil dibatalkan.');
        }

        return redirect()->back()->with('error', 'Pesanan tidak dapat dibatalkan.');
    }

    // --- 5. Fungsi Cek Ketersediaan Kamar (API) ---
    // (Opsional: Tetap disimpan jika nanti butuh fitur kalender canggih)
    public function checkAvailability(Request $request)
    {
        $kamarId = $request->kamar_id;
        
        if (!$kamarId) return response()->json([]);

        $kamar = Kamar::find($kamarId);
        if (!$kamar) return response()->json([]);

        $totalKamar = $this->getTotalStok($kamar->tipe_kamar);

        $bookings = Booking::where('kamar_id', $kamarId)
            ->where('status', '!=', 'cancelled')
            ->where('check_out', '>=', now())
            ->get();

        $dateCounts = [];

        foreach ($bookings as $booking) {
            $period = CarbonPeriod::create($booking->check_in, Carbon::parse($booking->check_out)->subDay());

            foreach ($period as $date) {
                $dateString = $date->format('Y-m-d');
                if (!isset($dateCounts[$dateString])) {
                    $dateCounts[$dateString] = 0;
                }
                $dateCounts[$dateString] += $booking->jumlah_kamar;
            }
        }

        $fullDates = [];
        foreach ($dateCounts as $date => $count) {
            if ($count >= $totalKamar) {
                $fullDates[] = $date;
            }
        }

        return response()->json($fullDates);
    }

    // --- 6. Fungsi Invoice ---
    public function show($id)
    {
        $booking = Booking::with('kamar')->findOrFail($id);

        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('user.invoice', compact('booking'));
    }
}