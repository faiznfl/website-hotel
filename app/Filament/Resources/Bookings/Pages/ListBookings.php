<?php

namespace App\Filament\Resources\Bookings\Pages;

use App\Filament\Resources\Bookings\BookingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBookings extends ListRecords
{
    protected static string $resource = BookingResource::class;

    protected ?string $heading = 'Data Booking Customer';

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
