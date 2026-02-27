<?php

namespace App\Filament\Resources\Galleries;

use BackedEnum;
use App\Models\Gallery;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;
use App\Filament\Resources\Galleries\Pages\EditGallery;
use App\Filament\Resources\Galleries\Pages\CreateGallery;
use App\Filament\Resources\Galleries\Pages\ListGalleries;

class GalleryResource extends Resource
{
    protected static ?string $model = Gallery::class;

    protected static string|BackedEnum|null $navigationIcon = 'solar-gallery-bold';

    protected static ?string $recordTitleAttribute = 'keterangan'; // Ubah title ke keterangan agar lebih rapi

    protected static ?string $navigationLabel = 'Galeri';

    protected static string | \UnitEnum | null $navigationGroup = 'Website & Feedback';
    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Manajemen Foto Galeri')
                    ->description('Upload foto umum hotel atau foto spesifik untuk kamar tertentu.')
                    ->schema([
                        // 1. UPLOAD FOTO (Saya ubah 'gambar' jadi 'foto' agar sinkron dengan relasi Kamar sebelumnya)
                        FileUpload::make('foto')
                            ->label('Upload Foto')
                            ->image()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('800')
                            ->imageResizeTargetHeight('800')
                            ->directory('gallery-images') // Samakan foldernya dengan yang di KamarResource
                            ->visibility('public')
                            ->disk('public')
                            ->required()
                            ->columnSpanFull(),

                        // 2. KETERANGAN FOTO
                        TextInput::make('keterangan')
                            ->label('Keterangan Foto')
                            ->placeholder('Contoh: Kolam Renang, Lobi, atau Balkon Kamar')
                            ->maxLength(255)
                            ->required(),

                        // 3. PILIHAN KAMAR (Kunci agar bisa upload terpisah/bebas)
                        Select::make('kamar_id')
                            ->relationship('kamar', 'tipe_kamar') // Otomatis ambil nama tipe kamar dari relasi
                            ->label('Pilih Kamar (Opsional)')
                            ->placeholder('Pilih kamar...')
                            ->helperText('Kosongkan pilihan ini jika foto yang diupload adalah foto fasilitas umum hotel (bukan foto dalam kamar).')
                            ->searchable()
                            ->preload()
                            ->nullable(), // Wajib nullable agar bisa dikosongkan
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto') // Sesuai dengan database
                    ->label('Preview')
                    ->visibility('public')
                    ->disk('public')
                    ->size(80)
                    ->square(),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                // Menampilkan ini foto untuk kamar apa, atau foto umum
                TextColumn::make('kamar.tipe_kamar')
                    ->label('Terkait Dengan')
                    ->badge()
                    ->default('Fasilitas Umum (Hotel)') // Jika kamar_id kosong, tampilkan ini
                    ->color(fn ($state) => $state === 'Fasilitas Umum (Hotel)' ? 'success' : 'warning')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Diupload Pada')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // Sembunyikan default biar tabel tak penuh
            ])
            ->defaultSort('created_at', 'desc') // Otomatis urutkan dari foto terbaru
            ->actions([
                EditAction::make(),
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
            'index' => ListGalleries::route('/'),
            'create' => CreateGallery::route('/create'),
            'edit' => EditGallery::route('/{record}/edit'),
        ];
    }
}