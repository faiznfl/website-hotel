<?php

namespace App\Filament\Resources\Kamars\Pages;

use App\Filament\Resources\Kamars\KamarResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKamar extends CreateRecord
{
    protected static string $resource = KamarResource::class;
    public function getHeading(): string
    {
        return 'Tambah Kamar Baru'; // Silakan ganti kata-katanya di sini
    }

    public function getTitle(): string
    {
        return 'Tambah Kamar'; // Muncul di tab browser
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
}
