<?php

namespace App\Filament\Resources\Kamars\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UnitsRelationManager extends RelationManager
{
    protected static string $relationship = 'units';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
            TextInput::make('nomor_kamar')
                ->required()
                ->unique(ignoreRecord: true),
            Select::make('status')
                ->options([
                    'available' => 'Tersedia',
                    'booked' => 'Terisi',
                    'maintenance' => 'Perbaikan',
                ])->default('available')->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->poll('5s')
            ->recordTitleAttribute('nomor_kamar')
            ->columns([
                TextColumn::make('nomor_kamar')->sortable(),
                SelectColumn::make('status')
                ->options([
                    'available' => 'Tersedia',
                    'booked' => 'Terisi',
                    'maintenance' => 'Perbaikan',
                ]),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
