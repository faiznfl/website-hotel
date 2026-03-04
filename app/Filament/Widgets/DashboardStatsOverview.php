<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Contact;
use App\Models\Testimonial;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1; // Tetap di paling atas

    protected function getStats(): array
    {
        // 1. Booking Pending (Urgensi Tinggi)
        $pendingBookings = Booking::where('status', 'pending')->count();
        
        // 2. Total Pesan (Komunikasi)
        $totalContacts = Contact::count();

        // 3. Rata-rata Rating (Reputasi)
        $avgRating = Testimonial::avg('stars') ?? 0;

        return [
            Stat::make('Antrean Konfirmasi', $pendingBookings . ' Reservasi')
                ->description($pendingBookings > 0 ? 'Ada orderan yang perlu diproses' : 'Semua pesanan sudah diproses')
                ->descriptionIcon($pendingBookings > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-badge')
                ->color($pendingBookings > 0 ? 'danger' : 'success') 
                ->icon('heroicon-o-clipboard-document-check'),

            Stat::make('Pesan Masuk (Inbox)', $totalContacts . ' Pesan')
                ->description('Total interaksi tamu lewat kontak')
                ->descriptionIcon('heroicon-m-chat-bubble-bottom-center-text')
                ->color('info')
                ->chart([3, 7, 5, 12, 9, 15, $totalContacts]),

            Stat::make('Kepuasan Tamu', number_format($avgRating, 1) . ' / 5.0')
                ->description($avgRating >= 4 ? 'Reputasi Sangat Baik' : 'Perlu ditingkatkan')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color($avgRating >= 4 ? 'warning' : 'gray')
                ->icon('heroicon-o-face-smile'),
        ];
    }
}