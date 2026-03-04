<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PopularRoomChart extends ChartWidget
{
    protected ?string $heading = 'Kamar Paling Laris';

    protected static ?int $sort = 3; // Letakkan setelah tren bulanan
    
    protected int | string | array $columnSpan = 1; 
    
    protected ?string $maxHeight = '260px'; 

    protected function getData(): array
    {
        // Mengambil top 3 tipe kamar yang paling banyak di-booking
        $data = Booking::select('kamars.tipe_kamar', DB::raw('count(*) as total'))
            ->join('kamars', 'bookings.kamar_id', '=', 'kamars.id')
            ->where('bookings.status', 'confirmed')
            ->groupBy('kamars.tipe_kamar')
            ->orderBy('total', 'desc') // Pastikan yang paling banyak di atas
            ->limit(3)
            ->get();

        return [
            'labels' => $data->pluck('tipe_kamar')->toArray(),
            'datasets' => [
                [
                    'label' => 'Total Booking',
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => [
                        '#D4AF37', // Emas (Luxury/Suite)
                        '#1F2937', // Dark (Deluxe)
                        '#9CA3AF', // Gray (Standard)
                    ],
                    'hoverOffset' => 4,
                    'borderWidth' => 2,
                    'borderColor' => '#fff',
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    // Opsi tambahan agar chart tidak terlalu kaku
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom', // Legenda di bawah agar chart tetap besar
                ],
            ],
            'cutout' => '70%', // Bikin lubang tengahnya lebih besar (elegan)
        ];
    }
}