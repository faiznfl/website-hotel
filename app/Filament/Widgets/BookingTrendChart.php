<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class BookingTrendChart extends ChartWidget
{
    // Kembalikan ke static agar Filament tidak bingung saat render title
    protected ?string $heading = 'Grafik Performa Bulanan (Confirmed)';

    protected static ?int $sort = 2;
    
    // Lebar 2 kolom agar grafik terlihat jelas dan detail
    protected int | string | array $columnSpan = 'full'; 

    protected function getData(): array
    {
        $data = collect();
        $labels = collect();

        // Mengambil data 6 bulan terakhir
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            
            $count = Booking::whereMonth('created_at', $date->month)
                            ->whereYear('created_at', $date->year)
                            ->where('status', 'confirmed')
                            ->count();

            $data->push($count);
            
            // Format bulan jadi Bahasa Indonesia (Contoh: Januari, Februari)
            $labels->push($date->translatedFormat('F')); 
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Reservasi Sukses',
                    'data' => $data->toArray(),
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(251, 191, 36, 0.1)', // Emas lebih halus
                    'borderColor' => '#f59e0b', // Emas Solid sesuai tema hotel
                    'tension' => 0.4, // Bikin garis grafiknya lebih "smooth" melengkung
                    'pointBackgroundColor' => '#f59e0b',
                    'pointBorderColor' => '#fff',
                ],
            ],
            'labels' => $labels->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}