<?php

namespace App\Filament\Resources\MeetingReservations;

use App\Filament\Resources\MeetingReservations\Pages\CreateMeetingReservation;
use App\Filament\Resources\MeetingReservations\Pages\EditMeetingReservation;
use App\Filament\Resources\MeetingReservations\Pages\ListMeetingReservations;
use App\Filament\Resources\MeetingReservations\Schemas\MeetingReservationForm;
use App\Filament\Resources\MeetingReservations\Tables\MeetingReservationsTable;
use App\Models\MeetingReservation;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class MeetingReservationResource extends Resource
{
    protected static ?string $model = MeetingReservation::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Booking Ruangan';

    protected static ?string $recordTitleAttribute = 'nama_tamu';

    protected static string | \UnitEnum | null $navigationGroup = 'Manajemen Reservasi';
    protected static ?string $modelLabel = 'Booking Ruangan';
    protected static ?string $pluralModelLabel = 'Booking Ruangan';
    protected static ?int $navigationSort = 2; // Supaya paling atas

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
        ->schema([
                Section::make('Detail Perubahan Jadwal')
                    ->description('Sesuaikan waktu dan status reservasi di bawah ini.')
                    ->schema([
                        Select::make('meeting_id')
                            ->relationship('ruangan', 'judul')
                            ->label('Ruangan')
                            ->required()
                            ->searchable(),

                        Select::make('customer_id')
                            ->relationship('customer', 'name')
                            ->label('Pemesan')
                            ->required()
                            ->searchable(),

                        DatePicker::make('tanggal_booking')
                            ->label('Tanggal Booking')
                            ->required()
                            ->native(false) // Ini sudah cukup untuk memunculkan kalender cantik
                            ->displayFormat('d M Y')
                            ->prefixIcon('heroicon-m-calendar')
                            ->minDate(now()),

                        Grid::make(2)
                            ->schema([
                                TimePicker::make('jam_mulai')
                                    ->label('Jam Mulai')
                                    ->required()
                                    ->seconds(false),
                                
                                TimePicker::make('jam_selesai')
                                    ->label('Jam Selesai')
                                    ->required()
                                    ->seconds(false),
                            ]),

                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'canceled' => 'Canceled',
                            ])
                            ->required()
                            ->native(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ruangan.judul')
                    ->label('Ruangan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('customer.name')
                    ->label('Pemesan')
                    ->searchable(),

                TextColumn::make('tanggal_booking')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('jam_mulai')
                    ->label('Waktu')
                    ->formatStateUsing(fn ($record) => "{$record->jam_mulai} - {$record->jam_selesai}")
                    ->icon('heroicon-m-clock'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'confirmed' => 'success',
                        'pending' => 'warning',
                        'canceled' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('admin.name')
                    ->label('Handled By')
                    ->placeholder('Belum diproses')
                    ->description(fn ($record) => $record->admin_id ? 'Verified' : 'Waiting Action'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'canceled' => 'Canceled',
                    ]),
            ])
            ->actions([
                Action::make('chat_wa')
                    ->label('Hubungi WA')
                    ->icon('heroicon-m-chat-bubble-left-right')
                    ->color('success')
                    ->url(function (MeetingReservation $record) {
                        // SESUAIKAN: Pakai 'nomor_hp' sesuai Model User Kakak
                        $nomor = $record->customer?->nomor_hp; 

                        if (!$nomor) return null;

                        // Bersihkan karakter selain angka
                        $clean_phone = preg_replace('/[^0-9]/', '', $nomor);

                        // Format 08... ke 62...
                        if (str_starts_with($clean_phone, '0')) {
                            $clean_phone = '62' . substr($clean_phone, 1);
                        }

                        $pesan = urlencode("Halo Kak *{$record->customer->name}*, kami dari Admin Hotel...");

                        return "https://wa.me/{$clean_phone}?text={$pesan}";
                    })
                    ->openUrlInNewTab()
                    // Tombol hanya muncul jika ada nomornya di database
                    ->visible(fn ($record) => !empty($record->customer?->nomor_hp)),

                // Tombol Konfirmasi Cepat (Tanpa masuk halaman edit)
                Action::make('confirm')
                    ->label('Terima')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->hidden(fn ($record) => $record->status !== 'pending')
                    ->requiresConfirmation()
                    ->action(function (MeetingReservation $record) {
                        $record->update([
                            'status' => 'confirmed',
                            'admin_id' => Auth::id(),
                        ]);

                        Notification::make()
                            ->title('Reservasi Diterima')
                            ->success()
                            ->send();
                    }),

                // Tombol Edit (Untuk ubah jadwal/ruangan)
                EditAction::make()
                    ->color('info')
                    ->label('Ubah Jadwal'),
                
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
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
            'index' => ListMeetingReservations::route('/'),
            'create' => CreateMeetingReservation::route('/create'),
            'edit' => EditMeetingReservation::route('/{record}/edit'),
        ];
    }
}
