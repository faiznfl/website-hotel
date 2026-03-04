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
        $status = $request->status ?? 'semua';
        $kategori = $request->kategori ?? 'hotel'; // Ambil parameter kategori dari URL

        // Siapkan koleksi kosong agar tidak error di Blade
        $bookings = collect();
        $orders = collect();

        // 1. Ambil data Hotel (Jika kategori 'hotel' atau 'semua')
        if ($kategori === 'hotel' || $kategori === 'semua') {
            $queryHotel = \App\Models\Booking::whereDate('check_in', '>=', $awal)
                            ->whereDate('check_in', '<=', $akhir);
            
            // Filter status khusus hotel (confirmed, pending, dll)
            if ($status !== 'semua' && $kategori === 'hotel') {
                $queryHotel->where('status', $status);
            }
            $bookings = $queryHotel->orderBy('check_in', 'asc')->get();
        }

        // 2. Ambil data Restoran (Jika kategori 'restoran' atau 'semua')
        if ($kategori === 'restoran' || $kategori === 'semua') {
            $queryResto = \App\Models\Order::whereDate('created_at', '>=', $awal)
                            ->whereDate('created_at', '<=', $akhir);
            
            // Filter status khusus resto (Lunas, Belum Bayar)
            if ($status !== 'semua' && $kategori === 'restoran') {
                $queryResto->where('status_pembayaran', $status);
            }
            $orders = $queryResto->orderBy('created_at', 'asc')->get();
        }

        // Gabungkan semua data ke view
        $pdf = Pdf::loadView('laporan.pdf', compact('bookings', 'orders', 'awal', 'akhir', 'status', 'kategori'));
        
        return $pdf->setPaper('A4', 'landscape')->download("Laporan_{$kategori}_{$awal}.pdf");
    }
}