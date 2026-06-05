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
            $queryHotel = Booking::whereDate('check_in', '>=', $awal)
                            ->whereDate('check_in', '<=', $akhir);
            
            // --- PERBAIKAN LOGIKA FILTER STATUS HOTEL ---
            if ($status !== 'semua') {
                // Jika status yang dipilih admin adalah 'confirmed'
                if ($status === 'confirmed') {
                    // Tarik baik yang masih confirmed (menginap) maupun yang sudah checked_out
                    $queryHotel->whereIn('status', ['confirmed', 'checked_out']);
                } else {
                    // Jika memilih status lain (misal: 'cancelled' atau 'pending')
                    $queryHotel->where('status', $status);
                }
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