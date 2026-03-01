<?php

namespace App\Filament\Resources\Menus\Pages;

use App\Filament\Resources\Menus\MenuResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMenu extends CreateRecord
{
    protected static string $resource = MenuResource::class;
    public function getHeading(): string
    {
        return 'Tambah Menu Baru'; // Silakan ganti kata-katanya di sini
    }

    public function getTitle(): string
    {
        return 'Tambah Menu'; // Muncul di tab browser
    }
    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Simpan');
    }

    // 3. Ganti Tombol "Create & create another"
    protected function getCreateAnotherFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateAnotherFormAction()
            ->label('Simpan & Buat Baru');
    }

    // 4. Ganti Tombol "Cancel"
    protected function getCancelFormAction(): \Filament\Actions\Action
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
