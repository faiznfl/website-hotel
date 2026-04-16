<?php

namespace App\Filament\Resources\Contacts;

use App\Filament\Resources\Contacts\Pages\CreateContact;
use App\Filament\Resources\Contacts\Pages\EditContact;
use App\Filament\Resources\Contacts\Pages\ListContacts;
use App\Filament\Resources\Contacts\Schemas\ContactForm;
use App\Filament\Resources\Contacts\Tables\ContactsTable;
use App\Models\Contact;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

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
                                TextInput::make('nama')
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
                                Textarea::make('pesan')
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
            // Kolom Status Otomatis
            TextColumn::make('user_id')
            ->label('Status')
            ->badge()
            ->state(fn ($record) => $record->user_id ? 'DIBALAS' : 'PENDING') // Deteksi manual
            ->color(fn ($state) => $state === 'DIBALAS' ? 'success' : 'danger'),

            TextColumn::make('nama')
                ->label('Pengirim')
                ->weight('bold'),

            // Menampilkan Nama Admin yang membalas
            TextColumn::make('admin.name') // Ini mengambil relasi dari Model Contact
                ->label('Ditangani Oleh')
                ->placeholder('Menunggu respon...') // Muncul jika user_id masih kosong
                ->color('info'),

            TextColumn::make('created_at')
                ->label('Tgl Masuk')
                ->dateTime('d M Y, H:i'),
        ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                // INI TOMBOL STANDARD FILAMENT
                // Saat diklik, dia akan membuka Form di atas dalam mode Modal
                ViewAction::make(),

                // Tombol Balas WA (Tetap saya pertahankan karena aman & berguna)
                Action::make('reply_wa')
                ->label('Balas WA')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('success')
                ->requiresConfirmation()
                ->action(function (Contact $record, \Filament\Resources\Pages\ListRecords $livewire) {
                    // 1. Update Database (Ini sudah jalan di log tadi)
                    $record->update([
                        'user_id' => Auth::id(),
                    ]);

                    // 2. Format Nomor HP
                    $phone = preg_replace('/[^0-9]/', '', $record->phone);
                    if (str_starts_with($phone, '0')) {
                        $phone = '62' . substr($phone, 1);
                    }

                    // 3. Eksekusi JS via $livewire (Ini pengganti $this agar tidak error)
                    $livewire->js("window.open('https://api.whatsapp.com/send?phone={$phone}', '_blank')");
                    
                    // 4. Notifikasi
                    \Filament\Notifications\Notification::make()
                        ->title('Status Berhasil Diperbarui!')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->modalHeading('Balas via WhatsApp?')
                ->modalDescription('Setelah klik tombol ini, status akan berubah menjadi "Sudah Dibalas".'),

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

    public static function canViewAny(): bool
    {
        return Auth::user()->role === 'admin';
    }
}
