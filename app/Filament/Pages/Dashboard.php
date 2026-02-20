<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

use App\Filament\Widgets\BookingStatsOverview;
use App\Filament\Widgets\BookingTrendChart;
use App\Filament\Widgets\PopularRoomChart;
use App\Filament\Widgets\UpcomingSchedule;

class Dashboard extends BaseDashboard
{
    public function getColumns(): int | array
    {
        return 3;
    }

    public function getWidgets(): array
    {
        return [
            BookingStatsOverview::class,
            BookingTrendChart::class,
            PopularRoomChart::class,
            UpcomingSchedule::class,
        ];
    }
}