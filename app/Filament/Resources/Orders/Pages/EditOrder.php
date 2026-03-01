<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
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
    protected function getRedirectUrl(): string
    {
        // Kode ini menyuruh Filament kembali ke halaman 'index' (List Tabel)
        return $this->getResource()::getUrl('index'); 
    }
}
