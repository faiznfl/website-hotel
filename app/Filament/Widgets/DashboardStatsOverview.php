<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Contact;
use App\Models\Testimonial;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1; // Posisi paling atas

    protected function getStats(): array
    {
        // 1. Hitung Booking Pending (Orderan yang perlu diproses segera)
        $pendingBookings = Booking::where('status', 'pending')->count();
        
        // 2. Hitung Total Pesan Masuk
        $totalContacts = Contact::count();

        // 3. Hitung Rata-rata Rating Review
        $avgRating = Testimonial::avg('stars') ?? 0; // Jika kosong, set 0

        return [
            // KARTU 1: BOOKING PENDING (PENTING!)
            Stat::make('Menunggu Konfirmasi', $pendingBookings . ' Order')
                ->description($pendingBookings > 0 ? 'Segera proses orderan ini' : 'Semua aman')
                ->descriptionIcon($pendingBookings > 0 ? 'heroicon-m-exclamation-circle' : 'heroicon-m-check-circle')
                ->color($pendingBookings > 0 ? 'warning' : 'success') // Kuning kalau ada kerjaan, Hijau kalau santai
                ->icon('heroicon-o-ticket'),

            // KARTU 2: INBOX PESAN
            Stat::make('Total Pesan Masuk', $totalContacts)
                ->description('Pertanyaan dari tamu')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('primary')
                ->chart([5, 10, 8, 12, $totalContacts]) // Dummy chart kecil
                ->icon('heroicon-o-envelope'),

            // KARTU 3: KEPUASAN PELANGGAN
            Stat::make('Rata-rata Rating', number_format($avgRating, 1) . ' / 5.0')
                ->description('Berdasarkan ulasan tamu')
                ->color('warning') // Warna Emas/Kuning
                ->icon('heroicon-o-star'),
        ];
    }
}