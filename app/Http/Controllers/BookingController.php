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
    // --- HELPER: Function Khusus Stok (Biar tidak ditulis ulang-ulang) ---
    private function getTotalStok($tipeKamar)
    {
        $tipe = strtolower($tipeKamar);
        
        if (str_contains($tipe, 'superior')) return 9;
        if (str_contains($tipe, 'deluxe')) return 14;
        if (str_contains($tipe, 'family')) return 2;
        
        return 5; // Default
    }

    // --- 1. Fungsi Simpan Booking (Store) ---
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'nama_tamu' => 'required|string|max:255',
            'nomor_hp' => 'required|string',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'jumlah_kamar' => 'required|integer|min:1',
            'kamar_id' => 'nullable|exists:kamars,id',
            'tipe_kamar_manual' => 'nullable|string',
        ]);

        // 2. Logic Cari Kamar ID
        $kamarId = null;
        if ($request->filled('kamar_id')) {
            $kamarId = $request->kamar_id;
        } elseif ($request->filled('tipe_kamar_manual')) {
            $kamar = Kamar::where('tipe_kamar', $request->tipe_kamar_manual)->first();
            if ($kamar) {
                $kamarId = $kamar->id;
            } else {
                return back()->with('error', 'Maaf, tipe kamar tersebut tidak ditemukan.');
            }
        }

        // ============================================================
        // 3. VALIDASI STOK (Versi Lebih Rapi)
        // ============================================================
        
        $kamar = Kamar::find($kamarId);
        
        // Panggil Helper Function di atas
        $totalStokKamar = $this->getTotalStok($kamar->tipe_kamar);

        // Cek Stok Fisik
        if ($request->jumlah_kamar > $totalStokKamar) {
            return back()->with('error', "Maaf, tipe {$kamar->tipe_kamar} hanya memiliki total {$totalStokKamar} kamar.");
        }

        // Cek Ketersediaan Tanggal
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
            'status'       => 'pending',
        ]);

        return back()->with('success', "Reservasi Berhasil! Kode Booking: {$booking->kode_booking}.");
    }

    // --- 2. Fungsi Lihat Riwayat (History) ---
    public function history()
    {
        // PERBAIKAN: Tambahkan with('kamar') agar tidak N+1 Query (Biar Cepat)
        $bookings = Booking::with('kamar')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();
            
        return view('user.history', compact('bookings'));
    }

    // --- 3. Fungsi Batalkan Pesanan (Cancel) ---
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

    // --- 4. Fungsi Cek Ketersediaan Kamar (API) ---
    public function checkAvailability(Request $request)
    {
        $kamarId = $request->kamar_id;
        
        if (!$kamarId) return response()->json([]);

        $kamar = Kamar::find($kamarId);
        if (!$kamar) return response()->json([]);

        // Panggil Helper Function (Jadi tidak perlu tulis ulang angka 9, 14, 2)
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

    // --- 5. Fungsi Invoice ---
    public function show($id)
    {
        // Sudah bagus pakai with('kamar')
        $booking = Booking::with('kamar')->findOrFail($id);

        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('user.invoice', compact('booking'));
    }
}