<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Kamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    // Fungsi Simpan Booking
    public function store(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'nama_tamu' => 'required|string|max:255',
            'nomor_hp' => 'required|string',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'jumlah_kamar' => 'required|integer',
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

        // 3. Simpan ke Database
        $booking = Booking::create([
            'user_id'      => Auth::id(), // Pakai Auth::id()
            'kamar_id'     => $kamarId,
            'nama_tamu'    => $request->nama_tamu,
            'nomor_hp'     => $request->nomor_hp,
            'check_in'     => $request->check_in,
            'check_out'    => $request->check_out,
            'jumlah_kamar' => $request->jumlah_kamar,
            'status'       => 'pending',
        ]);

        // 4. Balikan Pesan Sukses
        return back()->with('success', "Reservasi Berhasil! Kode Booking Anda: {$booking->kode_booking}. Silakan cek menu Riwayat Pesanan.");
    }

    // Fungsi Lihat Riwayat (YANG ANDA TANYAKAN)
    public function history()
    {
        // Ambil booking milik user yang login
        $bookings = Booking::where('user_id', Auth::id())->latest()->get();
        
        // Kirim ke view (resources/views/user/history.blade.php)
        return view('user.history', compact('bookings'));
    }

    public function cancel($id)
    {
        // Cari booking milik user yang login
        $booking = \App\Models\Booking::where('id', $id)
            ->where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->firstOrFail();

        // Cek status
        if ($booking->status == 'pending') {
            $booking->status = 'cancelled';
            $booking->save();
            return redirect()->back()->with('success', 'Pesanan berhasil dibatalkan.');
        }

        return redirect()->back()->with('error', 'Pesanan tidak dapat dibatalkan.');
    }
}