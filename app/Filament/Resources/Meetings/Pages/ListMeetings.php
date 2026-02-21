<?php

namespace App\Filament\Resources\Meetings\Pages;

use App\Filament\Resources\Meetings\MeetingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMeetings extends ListRecords
{
    protected static string $resource = MeetingResource::class;

    protected ?string $heading = 'Data Meetings & Events';
    public function getTitle(): string
    {
        return 'Meetings & Events'; // Muncul di tab browser
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->label('Tambah Meetings & Events')
            ->icon('heroicon-o-plus'),
        ];
    }
}
