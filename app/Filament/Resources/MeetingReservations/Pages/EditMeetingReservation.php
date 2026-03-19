<?php

namespace App\Filament\Resources\MeetingReservations\Pages;

use App\Filament\Resources\MeetingReservations\MeetingReservationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMeetingReservation extends EditRecord
{
    protected static string $resource = MeetingReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
