<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PopularRoomChart extends ChartWidget
{
    protected ?string $heading = 'Kamar Favorit'; // HAPUS STATIC

    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 1; // LEBAR 1 KOLOM (KECIL)
    protected ?string $maxHeight = '260px'; 

    protected function getData(): array
    {
        $data = Booking::select('kamars.tipe_kamar', DB::raw('count(*) as total'))
            ->join('kamars', 'bookings.kamar_id', '=', 'kamars.id')
            ->where('bookings.status', 'confirmed')
            ->groupBy('kamars.tipe_kamar')
            ->limit(3)
            ->get();

        return [
            'labels' => $data->pluck('tipe_kamar')->toArray(),
            'datasets' => [
                [
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => ['#1f2937', '#f59e0b', '#9ca3af'], // Hitam, Emas, Abu
                    'borderWidth' => 0,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}