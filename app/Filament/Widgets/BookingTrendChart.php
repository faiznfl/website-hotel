<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class BookingTrendChart extends ChartWidget
{
    protected ?string $heading = 'Grafik Performa Bulanan'; // HAPUS STATIC

    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 2; // LEBAR 2 KOLOM (BESAR)

    protected function getData(): array
    {
        $data = collect();
        $labels = collect();

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = Booking::whereMonth('created_at', $date->month)
                            ->whereYear('created_at', $date->year)
                            ->where('status', 'confirmed')
                            ->count();

            $data->push($count);
            $labels->push($date->format('F')); 
        }

        return [
            'datasets' => [
                [
                    'label' => 'Transaksi Sukses',
                    'data' => $data->toArray(),
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(251, 191, 36, 0.2)', // Emas Transparan
                    'borderColor' => '#f59e0b', // Emas Solid
                    'tension' => 0.3, 
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