<?php

namespace App\Filament\Resources\Bookings;

use BackedEnum;
use App\Models\Booking;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Action as enter;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\ToggleButtons;
use App\Filament\Resources\Bookings\Pages\EditBooking;
use App\Filament\Resources\Bookings\Pages\ListBookings;
use App\Filament\Resources\Bookings\Pages\CreateBooking;
use App\Filament\Resources\Bookings\Schemas\BookingForm;
use App\Filament\Resources\Bookings\Tables\BookingsTable;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static string|BackedEnum|null $navigationIcon = 'zondicon-calendar';

    protected static ?string $navigationLabel = 'Booking';

    protected static ?string $recordTitleAttribute = 'Booking';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                
                // KELOMPOK 1: KOLOM KANAN (STATUS & TANGGAL)
                // Kita taruh status di atas atau samping agar mudah diakses
                Section::make('Status Reservasi')
                    ->description('Update status pembayaran/booking di sini')
                    ->schema([
                        ToggleButtons::make('status')
                        ->label('Update Status')
                        ->options([
                            'pending'   => 'Menunggu',
                            'confirmed' => 'Confirmed',
                            'cancelled' => 'Cancelled',
                        ])
                        ->colors([
                            'pending'   => 'warning',
                            'confirmed' => 'success',
                            'cancelled' => 'danger',
                        ])
                        ->icons([
                            'pending'   => 'heroicon-o-clock',
                            'confirmed' => 'heroicon-o-check-circle',
                            'cancelled' => 'heroicon-o-x-circle',
                        ])
                        ->inline() // Agar tombolnya berjejer ke samping
                        ->required(),
                    ])
                    ->columnSpan('full'), // Memanjang penuh

                // KELOMPOK 2: DATA TAMU (Read Only / Disabled)
                // Admin biasanya hanya melihat data ini, jarang mengubah nama tamu
                Section::make('Informasi Tamu')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('nama_tamu')
                                ->label('Nama Lengkap')
                                ->disabled() // Tidak bisa diedit sembarangan
                                ->dehydrated(false), // Agar tidak ikut terkirim saat save (opsional)
                            
                            TextInput::make('nomor_hp')
                                ->label('WhatsApp')
                                ->disabled()
                                ->dehydrated(false),
                        ]),
                    ]),

                // KELOMPOK 3: DETAIL KAMAR & TANGGAL
                // Ini bisa diedit jika tamu minta reschedule
                Section::make('Detail Reservasi')
                    ->icon('heroicon-o-calendar-days')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('kamar_id')
                                ->relationship('kamar', 'tipe_kamar')
                                ->label('Tipe Kamar')
                                ->required(),

                            TextInput::make('jumlah_kamar')
                                ->numeric()
                                ->default(1)
                                ->required(),

                            DatePicker::make('check_in')
                                ->label('Tanggal Check-In')
                                ->required(),

                            DatePicker::make('check_out')
                                ->label('Tanggal Check-Out')
                                ->required(),
                        ]),
                    ]),
            ]);
    }
    

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // 1. Tanggal Order Masuk
                TextColumn::make('created_at')
                    ->label('Tgl Order')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->color('gray'),

                // 2. Nama Tamu
                TextColumn::make('nama_tamu')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn (Booking $record) => $record->nomor_hp), // No HP muncul kecil di bawah nama

                // 3. Tipe Kamar
                TextColumn::make('kamar.tipe_kamar')
                    ->label('Kamar')
                    ->sortable()
                    ->badge() // Biar terlihat seperti label
                    ->color('info'),

                // 4. Tanggal Menginap
                TextColumn::make('check_in')
                    ->label('Jadwal')
                    ->date('d M')           
                    ->description(fn (Booking $record) => 's/d ' . \Carbon\Carbon::parse($record->check_out)->format('d M')),

                // 5. Status (Warna-warni)
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',   // Kuning
                        'confirmed' => 'success', // Hijau
                        'cancelled' => 'danger',  // Merah
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Menunggu',
                        'confirmed' => 'Confirmed',
                        'cancelled' => 'Batal',
                    }),
            ])
            ->defaultSort('created_at', 'desc') // Pesanan terbaru paling atas
            ->filters([
                // Filter berdasarkan Status
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Menunggu Konfirmasi',
                        'confirmed' => 'Confirmed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                EditAction::make(),
                
                // Tombol Cepat WA ke Tamu
                enter::make('hubungi')
                    ->label('Chat WA')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->url(fn (Booking $record) => "https://wa.me/{$record->nomor_hp}?text=Halo+{$record->nama_tamu},+terkait+reservasi+kamar+Anda...", true),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
        // return BookingsTable::configure($table);
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
            'index' => ListBookings::route('/'),
            'create' => CreateBooking::route('/create'),
            'edit' => EditBooking::route('/{record}/edit'),
        ];
    }
    // Matikan fitur "Create" (Buat Baru) bagi Admin
    public static function canCreate(): bool
    {
    return false;
    }
}
