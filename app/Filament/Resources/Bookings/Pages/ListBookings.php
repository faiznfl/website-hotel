<?php

namespace App\Filament\Resources\Bookings\Pages;

use App\Filament\Resources\Bookings\BookingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListBookings extends ListRecords
{
    protected static string $resource = BookingResource::class;

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua Pesanan'),
            
            'check_out_hari_ini' => Tab::make('Check-out Hari Ini')
                ->icon('heroicon-m-arrow-right-on-rectangle')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->whereDate('check_out', today())
                    ->where('status', 'confirmed') // Hanya yang sudah lunas/confirm
                )
                ->badge(static::getResource()::getModel()::whereDate('check_out', today())->where('status', 'confirmed')->count()),

            'sedang_menginap' => Tab::make('Sedang Menginap')
                ->icon('heroicon-m-moon')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->whereDate('check_in', '<=', today())
                    ->whereDate('check_out', '>', today())
                    ->where('status', 'confirmed')
                ),
            
            'selesai' => Tab::make('Sudah Check-out')
                ->icon('heroicon-m-check-badge')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'checked_out')),
        ];
    }

    protected ?string $heading = 'Data Booking Kamar Customer';

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
