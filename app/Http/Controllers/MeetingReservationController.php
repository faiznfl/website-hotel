<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MeetingReservation;
use Illuminate\Support\Facades\Auth;

class MeetingReservationController extends Controller
{
    public function store(Request $request, $id)
    {
        // 1. Validasi Input
        $request->validate([
            'tanggal_booking' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
        ], [
            'tanggal_booking.after_or_equal' => 'Tanggal tidak boleh hari kemarin.',
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
        ]);

        // 2. Simpan ke Database
        MeetingReservation::create([
            'meeting_id' => $id,
            'customer_id' => Auth::id(), // Mengambil ID user yang sedang login
            'tanggal_booking' => $request->tanggal_booking,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'status' => 'pending', // Default status
        ]);

        // 3. Kembali ke halaman sebelumnya dengan pesan sukses
        return back()->with('success', 'Reservasi Anda telah dikirim dan menunggu konfirmasi admin.');
    }
}