<?php

namespace App\Filament\Resources\Kamars;

use BackedEnum;
use App\Models\Kamar;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Actions\DeleteBulkAction;
use Filament\Schemas\Components\Group;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use App\Filament\Resources\Kamars\Pages\EditKamar;
use App\Filament\Resources\Kamars\Pages\ListKamars;
use App\Filament\Resources\Kamars\Pages\CreateKamar;
use Filament\Schemas\Components\Utilities\Set as set;

class KamarResource extends Resource
{
    protected static ?string $model = Kamar::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $navigationLabel = 'Data Kamar';

    protected static ?string $recordTitleAttribute = 'tipe_kamar';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3) // BAGI LAYAR JADI 3 KOLOM
            ->schema([
                
                // --- KOLOM KIRI (2 BAGIAN): DETAIL UTAMA ---
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        
                        // SECTION 1: INFO DASAR
                        Section::make('Informasi Kamar')
                            ->description('Nama kamar, deskripsi, dan media.')
                            ->icon('heroicon-m-information-circle')
                            ->schema([
                                Grid::make(2)->schema([
                                    Select::make('tipe_kamar')
                                        ->label('Tipe Kamar')
                                        ->options([
                                            'Superior Room' => 'Superior Room',
                                            'Deluxe Room'   => 'Deluxe Room',
                                            'Family Room'   => 'Family Room'
                                        ])
                                        ->prefixIcon('heroicon-m-tag')
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(fn (set $set, ?string $state) => $set('slug', Str::slug($state))),

                                    TextInput::make('slug')
                                        ->label('Link Slug')
                                        ->prefix('hotel.com/rooms/')
                                        ->disabled()
                                        ->dehydrated()
                                        ->required()
                                        ->unique(Kamar::class, 'slug', ignoreRecord: true),
                                ]),

                                RichEditor::make('deskripsi') // Pakai RichEditor biar bisa Bold/List
                                    ->label('Deskripsi Lengkap')
                                    ->toolbarButtons([
                                        'bold', 'italic', 'bulletList', 'orderedList', 'undo', 'redo',
                                    ])
                                    ->columnSpanFull(),

                                TagsInput::make('fasilitas') // UX BAGUS: Ketik koma/enter jadi tag
                                    ->label('Fasilitas')
                                    ->placeholder('Ketik fasilitas lalu tekan Enter (Cth: Wifi, AC, TV)')
                                    ->separator(',') // Simpan ke DB sebagai string dipisah koma
                                    ->splitKeys(['Tab', ','])
                                    ->columnSpanFull(),
                            ]),

                        // SECTION 2: MEDIA FOTO
                        Section::make('Media')
                            ->schema([
                                FileUpload::make('foto')
                                    ->hiddenLabel()
                                    ->image()
                                    ->imageEditor()
                                    ->imagePreviewHeight('250')
                                    ->directory('rooms')
                                    ->preserveFilenames()
                                    ->maxSize(10240) // 10MB
                                    ->columnSpanFull(),
                            ]),
                    ]),

                // --- KOLOM KANAN (1 BAGIAN): HARGA & SPESIFIKASI ---
                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        
                        // SECTION HARGA
                        Section::make('Harga')
                            ->schema([
                                TextInput::make('harga')
                                    ->label('Harga Per Malam')
                                    ->prefix('Rp')
                                    ->numeric()
                                    ->required(),
                            ]),

                        // SECTION KAPASITAS
                        Section::make('Spesifikasi')
                            ->icon('heroicon-m-adjustments-horizontal')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('max_dewasa')
                                        ->label('Dewasa')
                                        ->numeric()
                                        ->default(2)
                                        ->prefixIcon('heroicon-m-user'),
                                    
                                    TextInput::make('max_anak')
                                        ->label('Anak')
                                        ->numeric()
                                        ->default(1)
                                        ->prefixIcon('heroicon-m-face-smile'),
                                ]),

                                TextInput::make('beds')
                                    ->label('Jenis Kasur')
                                    ->placeholder('Cth: 1 King Bed')
                                    ->prefixIcon('heroicon-m-archive-box'), // Ikon kasur/box

                                TextInput::make('baths')
                                    ->label('Kamar Mandi')
                                    ->numeric()
                                    ->suffix('Unit')
                                    ->prefixIcon('heroicon-m-beaker'), // Ikon bath/air
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto')
                    ->label('Foto')
                    ->visibility('public')
                    ->disk('public')
                    ->square()
                    ->size(60), // Ukuran thumbnail pas
                
                TextColumn::make('tipe_kamar')
                    ->label('Tipe Kamar')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->badge() // Tampil sebagai Badge warna-warni
                    ->color(fn (string $state): string => match ($state) {
                        'Superior Room' => 'info',
                        'Deluxe Room'   => 'warning',
                        'Family Room'   => 'success',
                        default         => 'gray',
                    }),
                
                TextColumn::make('harga') 
                    ->label('Harga/Malam')
                    ->money('IDR', locale: 'id') // Format Rp otomatis
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),
                
                TextColumn::make('capacity')
                    ->label('Kapasitas')
                    ->getStateUsing(fn ($record) => "{$record->max_dewasa} Dewasa, {$record->max_anak} Anak")
                    ->color('gray'),

                TextColumn::make('fasilitas')
                    ->label('Fasilitas')
                    ->limit(30)
                    ->icon('heroicon-m-sparkles')
                    ->toggleable(isToggledHiddenByDefault: true), // Disembunyikan default biar tabel ga penuh
            ])
            ->defaultSort('tipe_kamar', 'asc')
            ->actions([
                EditAction::make()->iconButton(),   // Tombol jadi icon pencil
                DeleteAction::make()->iconButton(), // Tombol jadi icon tong sampah
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
            'index' => ListKamars::route('/'),
            'create' => CreateKamar::route('/create'),
            'edit' => EditKamar::route('/{record}/edit'),
        ];
    }
}