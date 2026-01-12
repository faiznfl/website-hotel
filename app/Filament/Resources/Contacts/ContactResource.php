<?php

namespace App\Filament\Resources\Contacts;

use BackedEnum;
use App\Models\Contact;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\Contacts\Pages\EditContact;
use App\Filament\Resources\Contacts\Pages\ListContacts;
use App\Filament\Resources\Contacts\Pages\CreateContact;
use App\Filament\Resources\Contacts\Schemas\ContactForm;
use App\Filament\Resources\Contacts\Tables\ContactsTable;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
        ->schema([
            TextInput::make('name')
                ->label('Nama Pengirim')
                ->readOnly(),
            
            TextInput::make('email')
                ->label('Email')
                ->readOnly(),

            TextInput::make('phone')
                ->label('Nomor WhatsApp / HP')
                ->tel() // Menambahkan ikon telepon
                ->readOnly(),

            Textarea::make('message')
                ->label('Isi Pesan')
                ->rows(5)
                ->columnSpanFull()
                ->readOnly(),
        ]);
        // return ContactForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
    return $table
        ->columns([
            TextColumn::make('name')
                ->searchable()
                ->sortable(),
            
            TextColumn::make('email')
                ->searchable(),

            TextColumn::make('phone')
                ->label('No. HP')
                ->icon('heroicon-m-phone'), // Ikon telepon biar cantik

            TextColumn::make('created_at')
                ->label('Diterima Pada')
                ->dateTime('d M Y, H:i')
                ->sortable(),
        ])
        ->actions([
            ViewAction::make(),
            DeleteAction::make(),
        ])
        ->bulkActions([
            DeleteBulkAction::make(),
        ])
        ->emptyStateActions([]);
        // return ContactsTable::configure($table);
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
            'index' => ListContacts::route('/'),
            'create' => CreateContact::route('/create'),
            'edit' => EditContact::route('/{record}/edit'),
        ];
    }
}
