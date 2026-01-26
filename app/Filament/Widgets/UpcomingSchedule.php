<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;

class UpcomingSchedule extends BaseWidget
{
    protected static ?string $heading = 'Jadwal Check-in / Check-out Terdekat';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full'; // LEBAR PENUH

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Filter: Booking Confirmed & Tanggalnya hari ini/masa depan
                Booking::query()
                    ->where('status', 'confirmed')
                    ->where(function($query) {
                        $query->whereDate('check_in', '>=', now())
                              ->orWhereDate('check_out', '>=', now());
                    })
                    ->orderBy('check_in', 'asc')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('nama_tamu')
                    ->weight('bold')
                    ->icon('heroicon-m-user'),

                TextColumn::make('kamar.tipe_kamar')
                    ->label('Kamar')
                    ->color('gray'),

                TextColumn::make('check_in')
                    ->label('Check In')
                    ->date('d M')
                    ->description(fn($record) => $record->check_in <= now() ? 'HARI INI' : ''),

                TextColumn::make('check_out')
                    ->label('Check Out')
                    ->date('d M')
                    ->color('danger'),

                TextColumn::make('status')
                    ->badge()
                    ->color('success'),
            ]);
    }
}