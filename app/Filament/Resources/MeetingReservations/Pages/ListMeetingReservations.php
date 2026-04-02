<?php

namespace App\Filament\Resources\MeetingReservations\Pages;

use App\Filament\Resources\MeetingReservations\MeetingReservationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMeetingReservations extends ListRecords
{
    protected static string $resource = MeetingReservationResource::class;

    protected ?string $heading = 'Data Booking Ruangan Customer';

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
