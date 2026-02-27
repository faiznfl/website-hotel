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
use Filament\Forms\Components\Repeater; // <-- TAMBAHAN IMPORT REPEATER
use App\Filament\Resources\Kamars\Pages\EditKamar;
use App\Filament\Resources\Kamars\Pages\ListKamars;
use App\Filament\Resources\Kamars\Pages\CreateKamar;
use Filament\Schemas\Components\Utilities\Set as set;

class KamarResource extends Resource
{
    protected static ?string $model = Kamar::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $navigationLabel = 'Kamar';

    protected static ?string $recordTitleAttribute = 'tipe_kamar';

    protected static string | \UnitEnum | null $navigationGroup = 'Data Master Hotel';
    protected static ?int $navigationSort = 2;

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

                                RichEditor::make('deskripsi') 
                                    ->label('Deskripsi Lengkap')
                                    ->toolbarButtons([
                                        'bold', 'italic', 'bulletList', 'orderedList', 'undo', 'redo',
                                    ])
                                    ->columnSpanFull(),

                                TagsInput::make('fasilitas') 
                                    ->label('Fasilitas')
                                    ->placeholder('Ketik fasilitas lalu tekan Enter (Cth: Wifi, AC, TV)')
                                    ->separator(',') 
                                    ->splitKeys(['Tab', ','])
                                    ->columnSpanFull(),
                            ]),

                        // SECTION 2: MEDIA FOTO UTAMA & GALERI (DIUPDATE)
                        Section::make('Manajemen Foto')
                            ->schema([
                                // A. Foto Utama / Cover
                                FileUpload::make('foto')
                                    ->label('Foto Utama (Cover Depan)')
                                    ->image()
                                    ->imageEditor()
                                    ->imagePreviewHeight('250')
                                    ->disk('public')
                                    ->directory('rooms')
                                    ->preserveFilenames()
                                    ->maxSize(10240) // 10MB
                                    ->columnSpanFull(),

                                // B. Foto Tambahan (Repeater ke tabel galleries)
                                Repeater::make('galleries')
                                    ->relationship() // Otomatis baca public function galleries() di Model Kamar
                                    ->label('Foto Tambahan (Untuk Slider Galeri Kamar)')
                                    ->schema([
                                        FileUpload::make('foto')
                                            ->label('Upload Foto Galeri')
                                            ->image()
                                            ->directory('gallery-images')
                                            ->disk('public')
                                            ->required(),
                                            
                                        TextInput::make('keterangan')
                                            ->label('Keterangan (Cth: Balkon, Kasur)')
                                            ->maxLength(255),
                                    ])
                                    ->columns(2)
                                    ->addActionLabel('Tambah Foto Lainnya')
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
                                    ->prefixIcon('heroicon-m-archive-box'), 

                                TextInput::make('baths')
                                    ->label('Kamar Mandi')
                                    ->numeric()
                                    ->suffix('Unit')
                                    ->prefixIcon('heroicon-m-beaker'), 
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
                    ->size(60), 
                
                TextColumn::make('tipe_kamar')
                    ->label('Tipe Kamar')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->badge() 
                    ->color(fn (string $state): string => match ($state) {
                        'Superior Room' => 'info',
                        'Deluxe Room'   => 'warning',
                        'Family Room'   => 'success',
                        default         => 'gray',
                    }),
                
                TextColumn::make('harga') 
                    ->label('Harga/Malam')
                    ->money('IDR', locale: 'id') 
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
                    ->toggleable(isToggledHiddenByDefault: true), 
            ])
            ->defaultSort('tipe_kamar', 'asc')
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