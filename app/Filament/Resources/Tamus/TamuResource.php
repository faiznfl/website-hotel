<?php

namespace App\Filament\Resources\Tamus;

use BackedEnum;
use App\Models\Tamu;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\Tamus\Pages\EditTamu;
use App\Filament\Resources\Tamus\Pages\ListTamus;
use App\Filament\Resources\Tamus\Pages\CreateTamu;
use App\Filament\Resources\Tamus\Schemas\TamuForm;
use App\Filament\Resources\Tamus\Tables\TamusTable;

class TamuResource extends Resource
{
    protected static ?string $model = Tamu::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Tamu';

    protected static ?string $navigationLabel = 'Tamu';

    public static function form(Schema $schema): Schema
    {
        return TamuForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('username')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('nama')->searchable(),
                TextColumn::make('alamat')->searchable(),
                TextColumn::make('no_telepon')->searchable(),
            ])
            ->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ]);
        // return TamusTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTamus::route('/'),
            'create' => CreateTamu::route('/create'),
            'edit' => EditTamu::route('/{record}/edit'),
        ];
    }
}
