<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Kamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// Tambahan library untuk manipulasi tanggal
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class BookingController extends Controller
{
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

        // 2. Logic Cari Kamar ID (Sama seperti sebelumnya)
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
        // 3. (BARU) VALIDASI STOK / KETERSEDIAAN KAMAR
        // ============================================================
        
        // A. Tentukan Batas Total Kamar (Sesuai aturan Hotel Anda)
        $kamar = Kamar::find($kamarId);
        $tipe = strtolower($kamar->tipe_kamar);
        
        $totalStokKamar = 0;
        if (str_contains($tipe, 'superior')) {
            $totalStokKamar = 9;
        } elseif (str_contains($tipe, 'deluxe')) {
            $totalStokKamar = 14;
        } elseif (str_contains($tipe, 'family')) {
            $totalStokKamar = 2; // Ini yang bikin Family cuma bisa 2
        } else {
            $totalStokKamar = 5;
        }

        // B. Cek apakah jumlah yang diminta melebihi total stok fisik?
        if ($request->jumlah_kamar > $totalStokKamar) {
            return back()->with('error', "Maaf, tipe {$kamar->tipe_kamar} hanya memiliki total {$totalStokKamar} kamar. Anda memesan {$request->jumlah_kamar}.");
        }

        // C. Cek Ketersediaan di TANGGAL TERSEBUT (Real-time check)
        // Kita hitung berapa kamar yang SUDAH terpakai di rentang tanggal ini
        $kamarTerpakai = Booking::where('kamar_id', $kamarId)
            ->where('status', '!=', 'cancelled') // Abaikan yang batal
            ->where(function ($query) use ($request) {
                // Logika Tabrakan Tanggal:
                // (CheckIn baru < CheckOut lama) DAN (CheckOut baru > CheckIn lama)
                $query->where('check_in', '<', $request->check_out)
                      ->where('check_out', '>', $request->check_in);
            })
            ->sum('jumlah_kamar'); // Jumlahkan total kamar yang sudah dibooking orang lain

        // D. Hitung Sisa
        $sisaKamar = $totalStokKamar - $kamarTerpakai;

        // E. Jika pesanan baru melebihi sisa kamar -> TOLAK
        if ($request->jumlah_kamar > $sisaKamar) {
            return back()->with('error', "Maaf, pada tanggal tersebut sisa kamar {$kamar->tipe_kamar} hanya tinggal {$sisaKamar} unit.");
        }

        // ============================================================
        // AKHIR VALIDASI STOK
        // ============================================================

        // 4. Simpan ke Database (Jika lolos validasi di atas)
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
        $bookings = Booking::where('user_id', Auth::id())->latest()->get();
        return view('user.history', compact('bookings'));
    }

    // --- 3. Fungsi Batalkan Pesanan (Cancel) ---
    public function cancel($id)
    {
        $booking = Booking::where('id', $id)
            ->where('user_id', Auth::id()) // Pakai Auth::id() biar lebih aman
            ->firstOrFail();

        if ($booking->status == 'pending') {
            $booking->status = 'cancelled';
            $booking->save();
            return redirect()->back()->with('success', 'Pesanan berhasil dibatalkan.');
        }

        return redirect()->back()->with('error', 'Pesanan tidak dapat dibatalkan.');
    }

    // --- 4. BARU: Fungsi Cek Ketersediaan Kamar (API) ---
    public function checkAvailability(Request $request)
    {
        $kamarId = $request->kamar_id;
        
        if (!$kamarId) {
            return response()->json([]);
        }

        // Ambil Data Kamar untuk cek Tipe
        $kamar = Kamar::find($kamarId);
        if (!$kamar) return response()->json([]);

        $tipe = strtolower($kamar->tipe_kamar);
        
        // Tentukan Total Stok Kamar Berdasarkan Tipe (Hardcode sesuai request)
        $totalKamar = 0;
        if (str_contains($tipe, 'superior')) {
            $totalKamar = 9;
        } elseif (str_contains($tipe, 'deluxe')) {
            $totalKamar = 14;
        } elseif (str_contains($tipe, 'family')) {
            $totalKamar = 2;
        } else {
            $totalKamar = 5; // Default jika ada tipe lain
        }

        // Ambil booking aktif (yang tidak cancel) di masa depan
        $bookings = Booking::where('kamar_id', $kamarId)
            ->where('status', '!=', 'cancelled')
            ->where('check_out', '>=', now())
            ->get();

        // Array untuk menghitung jumlah terpakai per tanggal
        $dateCounts = [];

        foreach ($bookings as $booking) {
            // Hitung range tanggal dari checkin sampai H-1 checkout
            $period = CarbonPeriod::create($booking->check_in, Carbon::parse($booking->check_out)->subDay());

            foreach ($period as $date) {
                $dateString = $date->format('Y-m-d');
                
                if (!isset($dateCounts[$dateString])) {
                    $dateCounts[$dateString] = 0;
                }
                
                // Tambahkan jumlah kamar yang dipesan
                $dateCounts[$dateString] += $booking->jumlah_kamar;
            }
        }

        // Cari tanggal mana yang jumlah pemakaiannya >= total kamar
        $fullDates = [];
        foreach ($dateCounts as $date => $count) {
            if ($count >= $totalKamar) {
                $fullDates[] = $date;
            }
        }

        // Kirim daftar tanggal penuh ke Frontend (Flatpickr)
        return response()->json($fullDates);
    }

    public function show($id)
{
    // 1. Cari booking berdasarkan ID
    $booking = Booking::with('kamar')->findOrFail($id);

    // 2. Keamanan: Pastikan yang melihat adalah pemilik booking itu sendiri
    if ($booking->user_id !== auth::id()) {
        abort(403, 'Unauthorized action.');
    }

    // 3. Tampilkan view invoice
    return view('user.invoice', compact('booking'));
}
}