<?php

namespace App\Filament\Widgets;

use App\Models\Kamar;
use App\Models\Contact;
use App\Models\Gallery;
use App\Models\Meeting;
use Illuminate\Support\Carbon; // <--- Import Carbon untuk tanggal
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsOverview extends BaseWidget
{
    // Hapus static agar aman dari error versi PHP/Filament
    protected ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        // 1. LOGIKA PESAN (Hitung pesan baru hari ini)
        $totalPesan = Contact::count();
        $pesanHariIni = Contact::whereDate('created_at', Carbon::today())->count();
        $deskripsiPesan = $pesanHariIni > 0 
            ? "Ada {$pesanHariIni} pesan baru hari ini!" 
            : 'Tidak ada pesan baru hari ini';
        $iconPesan = $pesanHariIni > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-check-circle';
        $warnaPesan = $pesanHariIni > 0 ? 'danger' : 'success'; // Merah kalau ada pesan baru (biar dibaca)

        // 2. LOGIKA KAMAR
        $totalKamar = Kamar::count();
        
        // 3. LOGIKA MEETING
        $totalMeeting = Meeting::count();

        // 4. LOGIKA GALERI (Foto terbaru minggu ini)
        $totalGaleri = Gallery::count();
        $galeriBaru = Gallery::whereDate('created_at', '>=', Carbon::now()->subDays(7))->count();

        return [
            // --- KARTU 1: PESAN MASUK (Bisa Diklik & Dinamis) ---
            Stat::make('Inbox Pesan', $totalPesan)
                ->description($deskripsiPesan)
                ->descriptionIcon($iconPesan)
                ->color($warnaPesan)
                ->chart([2, 10, 3, 12, 1, 15, $pesanHariIni]) // Grafik pura-pura naik
                ->icon('heroicon-o-envelope')
                // KUNCI: Tambahkan URL agar bisa diklik
                // Pastikan route-nya sesuai (filament.admin.resources.contacts.index)
                ->url(route('filament.admin.resources.contacts.index')), 

            // --- KARTU 2: DATA KAMAR ---
            Stat::make('Tipe Kamar', $totalKamar . ' Unit')
                ->description('Siap dipasarkan')
                ->descriptionIcon('heroicon-m-home')
                ->color('primary')
                ->chart([15, 4, 10, 2, 12, 4, 12])
                ->icon('heroicon-o-key')
                ->url(route('filament.admin.resources.kamars.index')),

            // --- KARTU 3: RUANG MEETING ---
            Stat::make('Fasilitas Meeting', $totalMeeting . ' Ruangan')
                ->description('MICE & Events')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('info')
                ->chart([3, 15, 4, 17, 2, 10, 15])
                ->icon('heroicon-o-presentation-chart-line')
                ->url(route('filament.admin.resources.meetings.index')),

            // --- KARTU 4: GALERI FOTO ---
            Stat::make('Total Galeri', $totalGaleri . ' Foto')
                ->description($galeriBaru > 0 ? "+{$galeriBaru} foto baru minggu ini" : 'Dokumentasi lengkap')
                ->descriptionIcon('heroicon-m-photo')
                ->color('warning')
                ->chart([8, 10, 5, 2, 20, 10, 15])
                ->icon('heroicon-o-camera')
                ->url(route('filament.admin.resources.galleries.index')),
        ];
    }
}