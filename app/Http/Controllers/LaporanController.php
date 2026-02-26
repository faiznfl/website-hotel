<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function cetakPdf(Request $request)
    {
        $awal = $request->awal;
        $akhir = $request->akhir;
        $status = $request->status ?? 'semua'; // Tangkap status

        // Query dasar (Filter Tanggal)
        $query = Booking::whereDate('check_in', '>=', $awal)
                        ->whereDate('check_in', '<=', $akhir);

        // Jika status BUKAN 'semua', tambahkan filter status
        if ($status !== 'semua') {
            $query->where('status', $status);
        }

        $bookings = $query->orderBy('check_in', 'asc')->get();

        $pdf = Pdf::loadView('laporan.pdf', compact('bookings', 'awal', 'akhir', 'status'));
        return $pdf->setPaper('A4', 'landscape')->download('Laporan_Reservasi_'.$awal.'_sd_'.$akhir.'.pdf');
    }
}