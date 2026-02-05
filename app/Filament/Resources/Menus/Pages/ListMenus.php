<?php

namespace App\Filament\Resources\Menus\Pages;

use App\Filament\Resources\Menus\MenuResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMenus extends ListRecords
{
    protected static string $resource = MenuResource::class;
    protected ?string $heading = 'Data Menu Restoran';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->label('Tambah Menu Restoran')
            ->icon('heroicon-o-plus'),
        ];
    }
}
