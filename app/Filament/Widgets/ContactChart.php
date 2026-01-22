<?php

namespace App\Filament\Widgets;

use App\Models\Contact;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ContactChart extends ChartWidget
{
    // REVISI: Hapus kata 'static' di sini
    protected ?string $heading = 'Statistik Pesan Masuk (1 Tahun Terakhir)';

    // Urutan Widget
    protected static ?int $sort = 2;

    // Lebar Widget
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = collect();
        $labels = collect();

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            
            $count = Contact::whereMonth('created_at', $date->month)
                            ->whereYear('created_at', $date->year)
                            ->count();
            
            $data->push($count);
            $labels->push($date->format('M Y'));
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pesan',
                    'data' => $data->toArray(),
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(250, 204, 21, 0.2)', // Kuning Transparan
                    'borderColor' => '#FACC15', // Kuning Solid
                    'tension' => 0.4,
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