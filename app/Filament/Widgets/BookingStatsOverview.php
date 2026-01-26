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
        // 1. Cek Tamu Check-in Hari Ini
        $checkInToday = Booking::whereDate('check_in', Carbon::today())
            ->where('status', 'confirmed')
            ->count();

        // 2. Hitung Estimasi Omzet Bulan Ini (Booking Confirmed)
        // Asumsi rata-rata per transaksi Rp 500.000 (Bisa disesuaikan)
        $transaksiBulanIni = Booking::whereMonth('created_at', Carbon::now()->month)
            ->where('status', 'confirmed')
            ->count();

        $estimasiOmzet = $transaksiBulanIni * 500000; 

        // 3. Persentase Okupansi (Kamar Terisi / Total Kamar)
        // Misal Total Kamar Hotel kamu ada 20 unit
        $totalKamar = 20; 

        $kamarTerpakai = Booking::whereDate('check_in', '<=', Carbon::today())
            ->whereDate('check_out', '>', Carbon::today())
            ->where('status', 'confirmed')
            ->sum('jumlah_kamar');

        $occupancyRate = ($totalKamar > 0) ? ($kamarTerpakai / $totalKamar) * 100 : 0;

        return [
            Stat::make('Omzet Bulan Ini', 'Rp ' . number_format($estimasiOmzet, 0, ',', '.'))
                ->description('Estimasi pendapatan kotor')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning') // Warna Emas
                ->chart([500000, 800000, 1200000, 1500000, $estimasiOmzet]),

            Stat::make('Tingkat Hunian', number_format($occupancyRate, 0) . '%')
                ->description($kamarTerpakai . ' kamar terisi hari ini')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color($occupancyRate > 50 ? 'success' : 'gray'),

            Stat::make('Check-In Hari Ini', $checkInToday . ' Tamu')
                ->description('Siapkan kunci kamar')
                ->descriptionIcon('heroicon-m-key')
                ->color('primary'),
        ];
    }
}