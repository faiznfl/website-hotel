<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingSchedule extends BaseWidget
{
    protected static ?string $heading = 'Agenda Check-in / Check-out Terdekat';
    
    // Kita taruh di paling bawah agar data angka & grafik terlihat lebih dulu
    protected static ?int $sort = 4; 
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Booking::query()
                    ->where('status', 'confirmed')
                    ->where(function($query) {
                        $query->whereDate('check_in', '>=', now()->subDay()) // Ambil yang baru masuk
                              ->orWhereDate('check_out', '>=', now());
                    })
                    ->orderBy('check_in', 'asc')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('nama_tamu')
                    ->label('Nama Tamu')
                    ->weight('bold')
                    ->searchable()
                    ->icon('heroicon-m-user-circle'),

                TextColumn::make('kamar.tipe_kamar')
                    ->label('Tipe Kamar')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('check_in')
                    ->label('Jadwal Masuk')
                    ->date('d F Y')
                    ->icon('heroicon-m-arrow-right-circle')
                    ->color(fn($record) => $record->check_in->isToday() ? 'success' : 'primary')
                    ->description(fn($record) => $record->check_in->isToday() ? 'HARI INI' : ''),

                TextColumn::make('check_out')
                    ->label('Jadwal Keluar')
                    ->date('d F Y')
                    ->icon('heroicon-m-arrow-left-circle')
                    ->color(fn($record) => $record->check_out->isToday() ? 'danger' : 'gray')
                    ->description(fn($record) => $record->check_out->isToday() ? 'Waktunya Check-out!' : ''),

                TextColumn::make('nomor_hp')
                    ->label('Kontak')
                    ->copyable()
                    ->icon('heroicon-m-phone')
                    ->color('info'),
            ])
            ->actions([
                // Tambahkan aksi cepat untuk melihat detail tanpa pindah halaman
                Action::make('Lihat')
                    ->url(fn (Booking $record): string => "/admin/bookings/{$record->id}")
                    ->icon('heroicon-m-eye')
            ]);
    }
}