<?php

namespace App\Filament\Resources\MeetingReservations;

use App\Filament\Resources\MeetingReservations\Pages\CreateMeetingReservation;
use App\Filament\Resources\MeetingReservations\Pages\EditMeetingReservation;
use App\Filament\Resources\MeetingReservations\Pages\ListMeetingReservations;
use App\Filament\Resources\MeetingReservations\Schemas\MeetingReservationForm;
use App\Filament\Resources\MeetingReservations\Tables\MeetingReservationsTable;
use App\Models\MeetingReservation;
use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

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
                Section::make('Informasi Utama')
                    ->description('Kelola detail reservasi dan ketersediaan waktu.')
                    ->schema([
                        // Baris 1: Ruangan & Pemesan (Dibuat lebar)
                        Grid::make(2)
                            ->schema([
                                Select::make('meeting_id')
                                    ->relationship('ruangan', 'judul')
                                    ->label('Ruangan')
                                    ->required()
                                    ->preload()
                                    ->searchable()
                                    ->prefixIcon('heroicon-m-home-modern'),

                                Select::make('customer_id')
                                    ->relationship('customer', 'name')
                                    ->label('Pemesan')
                                    ->required()
                                    ->searchable()
                                    ->prefixIcon('heroicon-m-user'),
                            ]),

                        // Baris 2: Tanggal, Jam, dan Status
                        Grid::make(3)
                            ->schema([
                                DatePicker::make('tanggal_booking')
                                    ->label('Tanggal Booking')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d M Y')
                                    ->prefixIcon('heroicon-m-calendar')
                                    ->minDate(now())
                                    ->rules([
                                        fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                            $mulai = $get('jam_mulai');
                                            $selesai = $get('jam_selesai');
                                            $ruanganId = $get('meeting_id');

                                            if (!$mulai || !$selesai || !$ruanganId) return;

                                            $bentrok = MeetingReservation::where('meeting_id', $ruanganId)
                                                ->where('tanggal_booking', $value)
                                                ->where('status', 'confirmed') // Hanya cek yang sudah fix
                                                ->where(function ($query) use ($mulai, $selesai) {
                                                    $query->where(function ($q) use ($mulai, $selesai) {
                                                        $q->where('jam_mulai', '<', $selesai)
                                                        ->where('jam_selesai', '>', $mulai);
                                                    });
                                                })
                                                // Jika sedang EDIT, abaikan data milik sendiri agar tidak bentrok dengan diri sendiri
                                                ->when($get('id'), fn ($q) => $q->where('id', '!=', $get('id')))
                                                ->exists();

                                            if ($bentrok) {
                                                $fail('Maaf, Ruangan ini sudah terisi pada jam tersebut. Silakan cari waktu lain.');
                                            }
                                        },
                                    ]),

                                TimePicker::make('jam_mulai')
                                    ->label('Jam Mulai')
                                    ->required()
                                    ->seconds(false)
                                    ->minutesStep(15)
                                    ->prefixIcon('heroicon-m-clock')
                                    ->reactive()
                                    ->afterStateUpdated(fn($set) => $set('jam_selesai', null)),

                                TimePicker::make('jam_selesai')
                                    ->label('Jam Selesai')
                                    ->required()
                                    ->seconds(false)
                                    ->minutesStep(15)
                                    ->prefixIcon('heroicon-m-stop-circle')
                                    ->after('jam_mulai')
                                    ->helperText('Minimal durasi 30 menit.'),
                            ]),

                        // Baris 3: Status (Dibuat Full Width atau ditaruh di bawah)
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'canceled' => 'Canceled',
                            ])
                            ->required()
                            ->native(false)
                            ->prefixIcon('heroicon-m-arrow-path'),
                    ])
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
                    ->description(fn ($record) => $record->customer?->nomor_hp ?? 'No Phone')
                    ->searchable(),

                TextColumn::make('tanggal_booking')
                    ->label('Jadwal Meeting')
                    ->formatStateUsing(function ($state) {
                        return \Carbon\Carbon::parse($state)
                            ->locale('id')
                            ->translatedFormat('l, d M Y'); 
                    })
                    ->sortable(),

                TextColumn::make('jam_mulai')
                    ->label('Waktu')
                    ->formatStateUsing(function ($record) {
                        $mulai = \Carbon\Carbon::parse($record->jam_mulai)->format('H:i');
                        $selesai = \Carbon\Carbon::parse($record->jam_selesai)->format('H:i');
                        return "{$mulai} - {$selesai}";
                    })
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

    public static function canViewAny(): bool
    {
        return Auth::user()->role === 'admin';
    }
}
