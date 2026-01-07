<?php

namespace App\Filament\Resources\Bookings;

use BackedEnum;
use Carbon\Carbon;
use App\Models\Kamar;
use App\Models\Booking;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Actions\Action as enter;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Actions\DeleteBulkAction;
use Filament\Schemas\Components\Group;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Utilities\Get;
use App\Filament\Resources\Bookings\Pages\EditBooking;
use App\Filament\Resources\Bookings\Pages\ListBookings;
use App\Filament\Resources\Bookings\Pages\CreateBooking;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Booking';

    protected static ?string $recordTitleAttribute = 'nama_tamu';

    public static function getStokKamar($tipe_kamar)
    {
        return match($tipe_kamar) {
            'Superior Room' => 9, 
            'Deluxe Room'   => 14,  
            'Family Room'   => 2,  
            default         => 5,
        };
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3) // KITA BAGI LAYAR JADI 3 BAGIAN
            ->schema([
                
                // --- KOLOM KIRI (2 BAGIAN): UTAMA (Detail Reservasi) ---
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Detail Reservasi')
                            ->description('Atur tipe kamar, jumlah, dan tanggal menginap.')
                            ->icon('heroicon-m-home-modern') // Icon Header Section
                            ->schema([
                                Grid::make(2)->schema([
                                    
                                    // 1. PILIH TIPE KAMAR
                                    Select::make('kamar_id')
                                        ->relationship('kamar', 'tipe_kamar')
                                        ->label('Tipe Kamar')
                                        ->prefixIcon('heroicon-m-key') // Icon input
                                        ->required()
                                        ->reactive() 
                                        ->afterStateUpdated(fn ($state, callable $set) => $set('check_in', null)),

                                    // 2. INPUT JUMLAH KAMAR
                                    TextInput::make('jumlah_kamar')
                                        ->label('Jumlah Kamar')
                                        ->numeric()
                                        ->default(1)
                                        ->minValue(1)
                                        ->required()
                                        ->prefixIcon('heroicon-m-calculator') // Icon input
                                        ->reactive() 
                                        
                                        // A. Validasi Maksimal Input (MANUAL)
                                        ->maxValue(function (Get $get) {
                                            $kamarId = $get('kamar_id');
                                            if (!$kamarId) return 10;
                                            $kamar = Kamar::find($kamarId);
                                            return self::getStokKamar($kamar->tipe_kamar);
                                        })
                                        
                                        // B. Helper Text (Info Stok)
                                        ->helperText(function (Get $get) {
                                            $kamarId = $get('kamar_id');
                                            if (!$kamarId) return 'Pilih kamar terlebih dahulu...';
                                            $kamar = Kamar::find($kamarId);
                                            $stok = self::getStokKamar($kamar->tipe_kamar);
                                            return "Sisa stok fisik: {$stok} unit";
                                        })
                                        ->afterStateUpdated(fn ($state, callable $set) => $set('check_in', null)),

                                    // 3. CHECK-IN
                                    DatePicker::make('check_in')
                                        ->label('Check-In')
                                        ->prefixIcon('heroicon-m-calendar')
                                        ->required()
                                        ->native(false) 
                                        ->displayFormat('d F Y')
                                        ->closeOnDateSelection()
                                        ->disabledDates(function (Get $get, $record) {
                                            // ... (LOGIKA VALIDASI TANGGAL - COPY DARI SEBELUMNYA) ...
                                            $kamarId = $get('kamar_id');
                                            $jumlahMauPesan = (int) $get('jumlah_kamar') ?: 1;
                                            if (!$kamarId) return [];
                                            $kamar = Kamar::find($kamarId);
                                            $totalStok = self::getStokKamar($kamar->tipe_kamar);
                                            
                                            if ($jumlahMauPesan > $totalStok) return self::blockAllDates();

                                            $bookings = Booking::where('kamar_id', $kamarId)
                                                ->where('status', '!=', 'cancelled')
                                                ->when($record, fn($q) => $q->where('id', '!=', $record->id)) 
                                                ->get();

                                            $dailyUsage = [];
                                            foreach ($bookings as $booking) {
                                                $start = Carbon::parse($booking->check_in);
                                                $end   = Carbon::parse($booking->check_out)->subDay(); 
                                                while ($start->lte($end)) {
                                                    $dateStr = $start->format('Y-m-d');
                                                    if (!isset($dailyUsage[$dateStr])) $dailyUsage[$dateStr] = 0;
                                                    $dailyUsage[$dateStr] += $booking->jumlah_kamar;
                                                    $start->addDay();
                                                }
                                            }
                                            $blockedDates = [];
                                            foreach ($dailyUsage as $date => $used) {
                                                if (($used + $jumlahMauPesan) > $totalStok) $blockedDates[] = $date;
                                            }
                                            return $blockedDates;
                                        }),

                                    // 4. CHECK-OUT
                                    DatePicker::make('check_out')
                                        ->label('Check-Out')
                                        ->prefixIcon('heroicon-m-calendar')
                                        ->required()
                                        ->native(false)
                                        ->displayFormat('d F Y')
                                        ->closeOnDateSelection()
                                        ->afterOrEqual('check_in')
                                        ->disabledDates(function (Get $get, $record) {
                                            // ... (LOGIKA VALIDASI TANGGAL SAMA PERSIS) ...
                                            $kamarId = $get('kamar_id');
                                            $jumlahMauPesan = (int) $get('jumlah_kamar') ?: 1;
                                            if (!$kamarId) return [];
                                            $kamar = Kamar::find($kamarId);
                                            $totalStok = self::getStokKamar($kamar->tipe_kamar);
                                            if ($jumlahMauPesan > $totalStok) return self::blockAllDates();

                                            $bookings = Booking::where('kamar_id', $kamarId)
                                                ->where('status', '!=', 'cancelled')
                                                ->when($record, fn($q) => $q->where('id', '!=', $record->id))
                                                ->get();
                                            $dailyUsage = [];
                                            foreach ($bookings as $booking) {
                                                $start = Carbon::parse($booking->check_in);
                                                $end   = Carbon::parse($booking->check_out)->subDay();
                                                while ($start->lte($end)) {
                                                    $dateStr = $start->format('Y-m-d');
                                                    if (!isset($dailyUsage[$dateStr])) $dailyUsage[$dateStr] = 0;
                                                    $dailyUsage[$dateStr] += $booking->jumlah_kamar;
                                                    $start->addDay();
                                                }
                                            }
                                            $blockedDates = [];
                                            foreach ($dailyUsage as $date => $used) {
                                                if (($used + $jumlahMauPesan) > $totalStok) $blockedDates[] = $date;
                                            }
                                            return $blockedDates;
                                        }),
                                ]),
                            ]),
                    ]),

                // --- KOLOM KANAN (1 BAGIAN): SIDEBAR (Status & Tamu) ---
                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        
                        // SECTION STATUS
                        Section::make('Status')
                            ->schema([
                                ToggleButtons::make('status')
                                ->label('') // Label dikosongin biar bersih
                                ->options([
                                    'pending'   => 'Pending',
                                    'confirmed' => 'Confirm',
                                    'cancelled' => 'Cancel',
                                ])
                                ->colors([
                                    'pending'   => 'warning',
                                    'confirmed' => 'success',
                                    'cancelled' => 'danger',
                                ])
                                ->icons([
                                    'pending'   => 'heroicon-m-clock',
                                    'confirmed' => 'heroicon-m-check-badge',
                                    'cancelled' => 'heroicon-m-x-circle',
                                ])
                                ->inline()
                                ->default('pending')
                                ->required(),
                            ]),

                        // SECTION DATA TAMU
                        Section::make('Data Tamu')
                            ->icon('heroicon-m-user-circle')
                            ->schema([
                                TextInput::make('nama_tamu')
                                    ->label('Nama Lengkap')
                                    ->prefixIcon('heroicon-m-user')
                                    ->disabled()
                                    ->dehydrated(false),
                                
                                TextInput::make('nomor_hp')
                                    ->label('WhatsApp')
                                    ->prefixIcon('heroicon-m-phone')
                                    ->disabled()
                                    ->dehydrated(false),
                            ]),
                    ]),
            ]);
    }
    
    // Helper blokir tanggal
    protected static function blockAllDates() {
        $now = Carbon::now();
        $end = Carbon::now()->addYears(5);
        $allDates = [];
        while ($now->lte($end)) {
            $allDates[] = $now->format('Y-m-d');
            $now->addDay();
        }
        return $allDates;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tgl Order')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->color('gray'),

                TextColumn::make('nama_tamu')
                    ->label('Tamu')
                    ->searchable()
                    ->weight('bold')
                    ->icon('heroicon-m-user') // Ada icon user
                    ->copyable() // BISA DI COPY
                    ->description(fn (Booking $record) => $record->nomor_hp),

                TextColumn::make('kamar.tipe_kamar')
                    ->label('Kamar')
                    ->sortable()
                    ->badge()
                    // Warna badge dinamis berdasarkan tipe kamar
                    ->color(fn (string $state): string => match ($state) {
                        'Superior Room' => 'info',
                        'Deluxe Room'   => 'warning',
                        'Family Room'   => 'success',
                        default         => 'gray',
                    }),
                
                TextColumn::make('jumlah_kamar')
                    ->label('Qty')
                    ->alignCenter()
                    ->formatStateUsing(fn (string $state): string => "{$state} Unit"), // Tambah kata 'Unit'

                TextColumn::make('check_in')
                    ->label('Durasi')
                    ->date('d M')
                    ->icon('heroicon-m-calendar-days')           
                    ->description(fn (Booking $record) => 's/d ' . \Carbon\Carbon::parse($record->check_out)->format('d M')),

                TextColumn::make('status')
                    ->badge()
                    ->icon(fn (string $state): string => match ($state) {
                        'pending'   => 'heroicon-m-arrow-path',
                        'confirmed' => 'heroicon-m-check',
                        'cancelled' => 'heroicon-m-x-mark',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending'   => 'warning',
                        'confirmed' => 'success',
                        'cancelled' => 'danger',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending'   => 'Menunggu',
                        'confirmed' => 'Confirmed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                EditAction::make()->iconButton(), // Jadi tombol icon kecil biar rapi
                enter::make('hubungi')
                    ->label('Chat')
                    ->icon('heroicon-m-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->iconButton() // Jadi tombol icon kecil biar rapi
                    ->url(fn (Booking $record) => "https://wa.me/{$record->nomor_hp}", true),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBookings::route('/'),
            'create' => CreateBooking::route('/create'),
            'edit' => EditBooking::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}