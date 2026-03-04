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
        // 1. Cek Tamu Check-in Hari Ini (Real-time)
        $checkInToday = Booking::whereDate('check_in', Carbon::today())
            ->where('status', 'confirmed')
            ->count();

        // 2. Hitung Omzet Bulan Ini (Data Real dari Database)
        $omzetBulanIni = Booking::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('status', 'confirmed')
            ->sum('total_harga'); 

        // 3. Persentase Okupansi (Kamar Terisi / Total Kamar)
        $totalKamar = 20; // Silakan ganti sesuai jumlah kamar asli Kakak
        
        // Menghitung kamar yang sedang ditempati hari ini
        $kamarTerpakai = Booking::where('status', 'confirmed')
            ->whereDate('check_in', '<=', Carbon::today())
            ->whereDate('check_out', '>', Carbon::today())
            ->sum('jumlah_kamar');

        $occupancyRate = ($totalKamar > 0) ? ($kamarTerpakai / $totalKamar) * 100 : 0;

        return [
            Stat::make('Omzet Bulan Ini', 'Rp ' . number_format($omzetBulanIni, 0, ',', '.'))
                ->description('Total pendapatan kotor terkonfirmasi')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success') // Hijau melambangkan profit
                ->chart([10, 25, 40, 30, 45, 60, 90]), // Grafik tren kecil (opsional)

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