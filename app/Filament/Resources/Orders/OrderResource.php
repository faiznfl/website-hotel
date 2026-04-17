<?php

namespace App\Filament\Resources\Orders;

use App\Filament\Resources\Orders\Pages\CreateOrder;
use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Models\Menu;
use App\Models\Order;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationLabel = 'Order Menu';
    protected static ?string $pluralModelLabel = 'Order Menu';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static string | \UnitEnum | null $navigationGroup = 'Manajemen Reservasi';
    protected static ?int $navigationSort = 3; // Supaya paling atas

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Group::make()
                    ->schema([
                        Section::make('Data Pelanggan')
                            ->description('Informasi identitas dan metode pembayaran.')
                            ->icon('heroicon-o-user')
                            ->schema([
                                TextInput::make('nama_pemesan')
                                    ->label('Nama Tamu')
                                    ->required()
                                    ->prefixIcon('heroicon-o-user'),

                                TextInput::make('info_pemesan')
                                    ->label('Nomor Kamar / Meja')
                                    ->prefixIcon('heroicon-o-home-modern'),

                                // TAMBAHAN: KETERANGAN METODE BAYAR DI FORM
                                TextInput::make('metode_pembayaran')
                                    ->label('Metode Pembayaran')
                                    ->formatStateUsing(fn ($state) => $state === 'online' ? '💳 QRIS / Online' : '💵 Tunai (Cash)')
                                    ->disabled() // Supaya tidak diubah manual oleh admin
                                    ->dehydrated(false), // Tidak perlu dikirim saat save jika hanya display

                                Select::make('status_pembayaran')
                                    ->label('Status Bayar')
                                    ->options([
                                        'Belum Bayar' => 'Belum Bayar',
                                        'Lunas' => 'Lunas',
                                        'Dibatalkan' => 'Dibatalkan',
                                    ])
                                    ->required()
                                    ->native(false),

                                Textarea::make('catatan')
                                    ->label('Catatan Dapur')
                                    ->columnSpanFull(),
                            ])->columns(2),
                    ])->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make('Keranjang')
                            ->icon('heroicon-o-shopping-cart')
                            ->schema([
                                Repeater::make('items')
                                    ->relationship('items')
                                    ->hiddenLabel()
                                    ->live()
                                    ->schema([
                                        Select::make('menu_id')
                                            ->label('Menu')
                                            ->relationship('menu', 'nama')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function ($state, Set $set) {
                                                $menu = Menu::find($state);
                                                if ($menu) {
                                                    $set('harga_satuan', $menu->harga);
                                                    $set('subtotal', $menu->harga);
                                                }
                                            })
                                            ->columnSpanFull(),

                                        TextInput::make('jumlah')
                                            ->label('Qty')
                                            ->numeric()
                                            ->default(1)
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn ($state, Get $get, Set $set) => 
                                                $set('subtotal', floatval($get('harga_satuan')) * floatval($state))
                                            )
                                            ->columnSpan(1),

                                        TextInput::make('subtotal')
                                            ->label('Subtotal')
                                            ->numeric()
                                            ->readOnly()
                                            ->prefix('Rp')
                                            ->columnSpan(2),

                                        Hidden::make('harga_satuan'), 
                                    ])
                                    ->columns(3)
                                    ->addActionLabel('Tambah Item'),

                                Group::make()
                                    ->schema([
                                        Placeholder::make('grand_total_placeholder')
                                            ->hiddenLabel()
                                            ->content(function (Get $get, Set $set) {
                                                $total = collect($get('items'))->sum('subtotal');
                                                $set('total_harga', $total);
                                                return new HtmlString('
                                                    <div class="text-right">
                                                        <span class="text-xs font-bold text-gray-500 uppercase">Total Tagihan</span>
                                                        <div class="text-3xl font-extrabold text-primary-600 mt-1">
                                                            Rp ' . number_format($total, 0, ',', '.') . '
                                                        </div>
                                                    </div>
                                                ');
                                            }),
                                        Hidden::make('total_harga')->default(0),
                                    ])
                                    ->extraAttributes(['class' => 'bg-gray-50 p-4 rounded-xl border border-gray-200 mt-4']),
                            ]),
                    ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M, H:i')
                    ->sortable(),

                TextColumn::make('nama_pemesan')
                    ->label('Pelanggan')
                    ->searchable()
                    ->description(fn (Order $record): string => $record->info_pemesan ?? '-')
                    ->weight('bold'),

                // TAMBAHAN: KOLOM METODE PEMBAYARAN DI TABEL
                TextColumn::make('metode_pembayaran')
                    ->label('Metode Bayar')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'online' => 'success',
                        'cash' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'online' => '💳 Digital',
                        'cash' => '💵 Tunai',
                        default => $state,
                    }),

                TextColumn::make('total_harga')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('status_pembayaran')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Lunas' => 'success',
                        'Belum Bayar' => 'danger',
                        'Dibatalkan' => 'gray',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return in_array(Auth::user()->role, ['admin', 'receptionist']);
    }
}
