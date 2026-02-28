<?php

namespace App\Filament\Resources\Orders;

use App\Filament\Resources\Orders\Pages\CreateOrder;
use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Orders\Schemas\OrderForm;
use App\Filament\Resources\Orders\Tables\OrdersTable;
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
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // === KOLOM KIRI: DATA PELANGGAN (Lebar 2/3) ===
                Group::make()
                    ->schema([
                        Section::make('Data Pelanggan')
                            ->description('Masukkan identitas pemesan di sini.')
                            ->icon(Heroicon::OutlinedUser) // Tambah Ikon
                            ->schema([
                                TextInput::make('nama_pemesan')
                                    ->label('Nama Tamu')
                                    ->required()
                                    ->maxLength(255)
                                    ->prefixIcon(Heroicon::OutlinedUser), // Ikon di dalam input

                                TextInput::make('info_pemesan')
                                    ->label('Nomor Kamar / Meja')
                                    ->placeholder('Cth: 104')
                                    ->maxLength(255)
                                    ->prefixIcon(Heroicon::OutlinedHomeModern),

                                Select::make('status_pembayaran')
                                    ->label('Status Bayar')
                                    ->options([
                                        'Belum Bayar' => 'Belum Bayar',
                                        'Lunas' => 'Lunas',
                                    ])
                                    ->default('Belum Bayar')
                                    ->required()
                                    ->native(false),

                                Textarea::make('catatan')
                                    ->label('Catatan Dapur')
                                    ->placeholder('Cth: Jangan pedas, es dipisah...')
                                    ->columnSpanFull(),
                            ])->columns(2), // Dibagi 2 kolom biar rapi
                    ])->columnSpan(['lg' => 2]), // Di layar besar makan 2 kolom

                // === KOLOM KANAN: KERANJANG (Lebar 1/3) ===
                Group::make()
                    ->schema([
                        Section::make('Keranjang')
                            ->icon(Heroicon::OutlinedShoppingCart)
                            ->schema([
                                Repeater::make('items')
                                    ->relationship('items')
                                    ->hiddenLabel() // Sembunyikan label "Items" biar bersih
                                    ->live()
                                    ->schema([
                                        Select::make('menu_id')
                                            ->label('Menu')
                                            ->relationship('menu', 'nama') // Sesuai kode Anda
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                $menu = Menu::find($state);
                                                if ($menu) {
                                                    $set('harga_satuan', $menu->harga);
                                                    $set('subtotal', $menu->harga);
                                                }
                                            })
                                            ->columnSpanFull(), // Menu ambil 1 baris penuh

                                        TextInput::make('jumlah')
                                            ->label('Qty')
                                            ->numeric()
                                            ->default(1)
                                            ->minValue(1)
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                                $harga = floatval($get('harga_satuan'));
                                                $jumlah = floatval($state);
                                                $set('subtotal', $harga * $jumlah);
                                            })
                                            ->columnSpan(1), // Kecil

                                        TextInput::make('subtotal')
                                            ->label('Subtotal')
                                            ->numeric()
                                            ->readOnly()
                                            ->prefix('Rp')
                                            ->columnSpan(2), // Agak lebar
                                            
                                        // Harga satuan kita sembunyikan saja (Hidden) biar rapi, 
                                        // tapi tetap ada buat hitungan
                                        Hidden::make('harga_satuan'), 
                                    ])
                                    ->columns(3)
                                    ->addActionLabel('Tambah Item'),

                                // TOTAL BESAR
                                Group::make()
                                    ->schema([
                                        Placeholder::make('grand_total_placeholder')
                                            ->hiddenLabel()
                                            ->content(function (Get $get, Set $set) {
                                                $total = 0;
                                                if (!empty($get('items'))) {
                                                    foreach ($get('items') as $item) {
                                                        $total += (int) ($item['subtotal'] ?? 0);
                                                    }
                                                }
                                                $set('total_harga', $total);
                                                
                                                // Tampilan Angka Besar
                                                return new HtmlString('
                                                    <div class="text-right">
                                                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Tagihan</span>
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
                    ])->columnSpan(['lg' => 1]), // Di layar besar makan 1 kolom
            ])->columns(3); // Total Grid Layout 3 Bagian
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('nama_pemesan')
                    ->label('Pelanggan')
                    ->searchable()
                    ->description(fn (Order $record): string => $record->info_pemesan ?? '-') // Info kamar jadi deskripsi kecil di bawah nama
                    ->weight('bold'),

                // SAYA TAMBAHKAN KOLOM TOTAL DI SINI BIAR KELIHATAN
                TextColumn::make('total_harga')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('status_pembayaran')
                    ->badge()
                    ->colors([
                        'danger' => 'Belum Bayar',
                        'success' => 'Lunas',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                EditAction::make()->iconButton(), // Jadi tombol ikon kecil
                DeleteAction::make()->iconButton(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }
}
