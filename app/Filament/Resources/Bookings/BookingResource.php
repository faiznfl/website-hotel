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
use Filament\Schemas\Components\Grid;
use Filament\Actions\DeleteBulkAction;
use Filament\Schemas\Components\Group;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder; // Tambahan penting biar ringan
use App\Filament\Resources\Bookings\Pages\EditBooking;
use App\Filament\Resources\Bookings\Pages\ListBookings;
use App\Filament\Resources\Bookings\Pages\CreateBooking;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Booking';

    protected static ?string $recordTitleAttribute = 'nama_tamu';

    protected static string | \UnitEnum | null $navigationGroup = 'Manajemen Reservasi';
    protected static ?int $navigationSort = 1; // Supaya paling atas

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    // --- OPTIMASI 1: Eager Loading (Biar Tabel Gak Lemot N+1) ---
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['kamar']);
    }

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
            ->columns(3)
            ->schema([
                
                // --- KOLOM KIRI ---
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Detail Reservasi')
                            ->description('Atur tipe kamar, jumlah, dan tanggal menginap.')
                            ->icon('heroicon-m-home-modern')
                            ->schema([
                                Grid::make(2)->schema([
                                    
                                    // 1. TIPE KAMAR
                                    Select::make('kamar_id')
                                        ->relationship('kamar', 'tipe_kamar')
                                        ->label('Tipe Kamar')
                                        ->prefixIcon('heroicon-m-key')
                                        ->required()
                                        ->reactive() 
                                        ->afterStateUpdated(fn ($state, callable $set) => $set('check_in', null)),

                                    // 2. JUMLAH KAMAR
                                    TextInput::make('jumlah_kamar')
                                        ->label('Jumlah Kamar')
                                        ->numeric()
                                        ->default(1)
                                        ->minValue(1)
                                        ->required()
                                        ->prefixIcon('heroicon-m-calculator')
                                        ->reactive() 
                                        ->maxValue(function (Get $get) {
                                            $kamarId = $get('kamar_id');
                                            if (!$kamarId) return 10;
                                            $kamar = Kamar::find($kamarId);
                                            return self::getStokKamar($kamar->tipe_kamar);
                                        })
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
                                            $kamarId = $get('kamar_id');
                                            $jumlahMauPesan = (int) $get('jumlah_kamar') ?: 1;
                                            if (!$kamarId) return [];
                                            
                                            // Ambil info kamar
                                            $kamar = Kamar::find($kamarId);
                                            $totalStok = self::getStokKamar($kamar->tipe_kamar);
                                            
                                            // OPTIMASI 2: Cuma ambil booking MASA DEPAN. 
                                            // Booking tahun lalu GAK PERLU dihitung. Bikin berat.
                                            $bookings = Booking::where('kamar_id', $kamarId)
                                                ->where('status', '!=', 'cancelled')
                                                ->where('check_out', '>=', now()) // <--- INI KUNCI BIAR GAK LAG
                                                ->when($record, fn($q) => $q->where('id', '!=', $record->id)) 
                                                ->get();

                                            $dailyUsage = [];
                                            foreach ($bookings as $booking) {
                                                $start = Carbon::parse($booking->check_in);
                                                $end   = Carbon::parse($booking->check_out)->subDay(); 
                                                
                                                // Batasi loop cuma sampai 1 tahun ke depan biar gak infinite loop
                                                if ($start->diffInDays($end) > 365) continue; 

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
                                        // Logic disabledDates SAMA PERSIS dengan di atas (Copy-Paste)
                                        ->disabledDates(function (Get $get, $record) {
                                            $kamarId = $get('kamar_id');
                                            $jumlahMauPesan = (int) $get('jumlah_kamar') ?: 1;
                                            if (!$kamarId) return [];

                                            $kamar = Kamar::find($kamarId);
                                            $totalStok = self::getStokKamar($kamar->tipe_kamar);

                                            // OPTIMASI QUERY (Sama seperti check_in)
                                            $bookings = Booking::where('kamar_id', $kamarId)
                                                ->where('status', '!=', 'cancelled')
                                                ->where('check_out', '>=', now()) // <--- Optimization
                                                ->when($record, fn($q) => $q->where('id', '!=', $record->id))
                                                ->get();

                                            $dailyUsage = [];
                                            foreach ($bookings as $booking) {
                                                $start = Carbon::parse($booking->check_in);
                                                $end   = Carbon::parse($booking->check_out)->subDay();
                                                if ($start->diffInDays($end) > 365) continue; 

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

                // --- KOLOM KANAN ---
                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Section::make('Status & Info')
                            ->schema([
                                ToggleButtons::make('status')
                                    ->label('')
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
    
    // Helper blokir tanggal (Tidak dipakai di form, tapi aman disimpan)
    protected static function blockAllDates() {
        // ... kode lama ...
        return [];
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
                    ->icon('heroicon-m-user')
                    ->copyable()
                    ->description(fn (Booking $record) => $record->nomor_hp),

                TextColumn::make('kamar.tipe_kamar') // Ini sudah dioptimasi lewat getEloquentQuery
                    ->label('Kamar')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Superior Room' => 'info',
                        'Deluxe Room'   => 'warning',
                        'Family Room'   => 'success',
                        default         => 'gray',
                    }),
                
                TextColumn::make('jumlah_kamar')
                    ->label('Qty')
                    ->alignCenter()
                    ->formatStateUsing(fn (string $state): string => "{$state} Unit"),

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
                EditAction::make()->iconButton(),
                enter::make('hubungi')
                    ->label('Chat')
                    ->icon('heroicon-m-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->iconButton()
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