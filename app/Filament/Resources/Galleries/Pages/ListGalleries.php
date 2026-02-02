<?php

namespace App\Filament\Resources\Galleries\Pages;

use App\Filament\Resources\Galleries\GalleryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGalleries extends ListRecords
{
    protected static string $resource = GalleryResource::class;

    protected ?string $heading = 'Data Galeri';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->label('Tambah Gambar')
            ->icon('heroicon-o-plus'),
        ];
    }
}
