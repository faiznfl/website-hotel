<?php

namespace App\Filament\Resources\Contacts;

use BackedEnum;
use App\Models\Contact;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Actions\ViewAction;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Schemas\Components\Group;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\Contacts\Pages\EditContact;
use App\Filament\Resources\Contacts\Pages\ListContacts;
use App\Filament\Resources\Contacts\Pages\CreateContact;
use App\Filament\Resources\Contacts\Schemas\ContactForm;
use App\Filament\Resources\Contacts\Tables\ContactsTable;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static ?string $navigationLabel = 'Contact';

    protected static string | \UnitEnum | null $navigationGroup = 'Website & Feedback';
    protected static ?int $navigationSort = 6;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
           ->schema([
                // Kita gunakan Group & Grid standar biar rapi, tapi isinya TextInput biasa
                Group::make()
                    ->schema([
                        Section::make('Detail Pengirim')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama')
                                    ->disabled(), // <--- KITA DISABLE (ABU-ABU)

                                TextInput::make('email')
                                    ->label('Email')
                                    ->disabled(),

                                TextInput::make('phone')
                                    ->label('WhatsApp')
                                    ->disabled(),
                            ])->columns(3), // Bagi 3 kolom
                    ])->columnSpanFull(),

                Group::make()
                    ->schema([
                        Section::make('Isi Pesan')
                            ->schema([
                                Textarea::make('message')
                                    ->hiddenLabel()
                                    ->rows(5)
                                    ->disabled()
                                    ->columnSpanFull(),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->icon('heroicon-m-envelope')
                    ->searchable(),

                TextColumn::make('phone')
                    ->label('WhatsApp')
                    ->icon('heroicon-m-phone'),

                TextColumn::make('created_at')
                    ->label('Masuk')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                // INI TOMBOL STANDARD FILAMENT
                // Saat diklik, dia akan membuka Form di atas dalam mode Modal
                ViewAction::make(),

                // Tombol Balas WA (Tetap saya pertahankan karena aman & berguna)
                Action::make('reply_wa')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->url(fn (Contact $record) => "https://wa.me/{$record->phone}", true),

                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
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
        ];
    }
}
