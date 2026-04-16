<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MeetingReservation;
use Illuminate\Support\Facades\Auth;

class MeetingReservationController extends Controller
{
    public function store(Request $request, $id)
    {
        // 1. Validasi Input Dasar
        $request->validate([
            'tanggal_booking' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
        ], [
            'tanggal_booking.after_or_equal' => 'Tanggal tidak boleh hari kemarin.',
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
        ]);

        // --- TAMBAHAN LOGIKA ANTI-BENTROK ---
        $mulai = $request->jam_mulai;
        $selesai = $request->jam_selesai;
        $tanggal = $request->tanggal_booking;

        // Cek apakah ada jadwal yang bertabrakan di ruangan & tanggal yang sama
        $bentrok = MeetingReservation::where('meeting_id', $id)
            ->where('tanggal_booking', $tanggal)
            ->whereIn('status', ['pending', 'confirmed']) // Abaikan yang sudah 'canceled'
            ->where(function ($query) use ($mulai, $selesai) {
                $query->where('jam_mulai', '<', $selesai)
                      ->where('jam_selesai', '>', $mulai);
            })
            ->exists();

        if ($bentrok) {
            return back()
                ->withErrors(['jam_mulai' => 'Maaf, ruangan sudah dipesan pada jam tersebut. Silakan pilih waktu lain.'])
                ->withInput(); // Agar user tidak perlu ngetik ulang tanggalnya
        }
        // ------------------------------------

        // 2. Simpan ke Database (Hanya jalan jika TIDAK bentrok)
        MeetingReservation::create([
            'meeting_id' => $id,
            'customer_id' => Auth::id(), 
            'tanggal_booking' => $tanggal,
            'jam_mulai' => $mulai,
            'jam_selesai' => $selesai,
            'status' => 'pending',
        ]);

        // 3. Kembali ke halaman sebelumnya dengan pesan sukses
        return back()->with('success', 'Reservasi Anda telah dikirim dan menunggu konfirmasi admin.');
    }
}