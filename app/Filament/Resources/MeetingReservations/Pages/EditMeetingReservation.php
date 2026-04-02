<?php

namespace App\Filament\Resources\MeetingReservations\Pages;

use App\Filament\Resources\MeetingReservations\MeetingReservationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditMeetingReservation extends EditRecord
{
    protected static string $resource = MeetingReservationResource::class;

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
        ->label('Simpan Perubahan');
    }
    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->label('Batal');
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        // Kode ini menyuruh Filament kembali ke halaman 'index' (List Tabel)
        return $this->getResource()::getUrl('index'); 
    }
}
