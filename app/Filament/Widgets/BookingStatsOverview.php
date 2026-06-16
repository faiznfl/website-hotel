<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Booking;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BookingStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // 1. Cek Tamu Check-in Hari Ini (Tetap 'confirmed' karena agenda masuk)
        $checkInToday = Booking::whereDate('check_in', Carbon::today())
            ->where('status', 'confirmed')
            ->count();

        // 2. FIX OMZET: Ubah 'where' menjadi 'whereIn' agar checked_out tetap terhitung uangnya
        $omzetBulanIni = Booking::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->whereIn('status', ['confirmed', 'checked_out']) // <-- Kunci Perbaikan
            ->sum('total_harga'); 

        // 3. Persentase Okupansi (Kamar Terisi / Total Kamar)
        $totalKamar = 20; 
        
        // FIX OKUPANSI: Tamu yang checked_out hari ini atau sedang stay tetap dihitung mengisi kamar
        $kamarTerpakai = Booking::whereIn('status', ['confirmed', 'checked_out']) // <-- Kunci Perbaikan
            ->whereDate('check_in', '<=', Carbon::today())
            ->whereDate('check_out', '>', Carbon::today())
            ->sum('jumlah_kamar');

        $occupancyRate = ($totalKamar > 0) ? ($kamarTerpakai / $totalKamar) * 100 : 0;

        return [
            Stat::make('Omzet Bulan Ini', 'Rp ' . number_format($omzetBulanIni, 0, ',', '.'))
                ->description('Total pendapatan kotor terkonfirmasi')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success') 
                ->chart([10, 25, 40, 30, 45, 60, 90]), 

            Stat::make('Tingkat Hunian', number_format($occupancyRate, 0) . '%')
                ->description($kamarTerpakai . ' dari ' . $totalKamar . ' kamar terisi')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color($occupancyRate > 70 ? 'success' : ($occupancyRate > 30 ? 'warning' : 'danger')),

            Stat::make('Check-In Hari Ini', $checkInToday . ' Reservasi')
                ->description('Segera siapkan pelayanan tamu')
                ->descriptionIcon('heroicon-m-key')
                ->color('primary'),
        ];
    }
}