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
use Filament\Forms\Components\Grid;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid as enter;
use App\Filament\Resources\Kamars\Pages\EditKamar;
use App\Filament\Resources\Kamars\Pages\ListKamars;
use App\Filament\Resources\Kamars\Pages\CreateKamar;
use App\Filament\Resources\Kamars\Schemas\KamarForm;
use App\Filament\Resources\Kamars\Tables\KamarsTable;
use Filament\Schemas\Components\Utilities\Set as set;

class KamarResource extends Resource
{
    protected static ?string $model = Kamar::class;

    protected static string|BackedEnum|null $navigationIcon = 'gmdi-meeting-room'; // Saya ubah icon default karena gmdi kadang perlu plugin khusus

    protected static ?string $recordTitleAttribute = 'tipe_kamar'; // Ganti jadi tipe_kamar biar pencarian enak
    protected static ?string $navigationLabel = 'Kamar';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                
                // 2. MODIFIKASI SELECT TIPE KAMAR
                Select::make('tipe_kamar')
                    ->options([
                        'Superior Room' => 'Superior Room',
                        'Deluxe Room' => 'Deluxe Room',
                        'Family Room' => 'Family Room'
                    ])
                    ->label('Tipe Kamar')
                    ->placeholder('Pilih Tipe Kamar')
                    ->required()
                    ->live() // Agar bereaksi saat dipilih
                    ->afterStateUpdated(fn (set $set, ?string $state) => $set('slug', Str::slug($state))), // Auto Generate Slug

                // 3. TAMBAHKAN INPUT SLUG DISINI
                TextInput::make('slug')
                    ->label('URL Slug (Otomatis)')
                    ->disabled()
                    ->dehydrated()
                    ->required()
                    ->unique(Kamar::class, 'slug', ignoreRecord: true),

                TextInput::make('harga')
                    ->label('Harga Kamar Per Malam')
                    ->placeholder('Masukan Harga Per Malam')
                    ->prefix('Rp')
                    ->numeric()
                    ->required(),
                    
                FileUpload::make('foto')
                    ->image()
                    ->imageEditor()
                    ->imagePreviewHeight('250')
                    ->directory('rooms') // Folder penyimpanan
                    ->preserveFilenames()
                    ->maxSize(10240)
                    ->label('Foto Kamar')
                    ->placeholder('Masukkan Foto Kamar')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg'])
                    ->visibility('public')
                    ->disk('public')
                    ->required(),

                    Textarea::make('deskripsi')
                    ->label('Deskripsi Kamar')
                    ->rows(4)
                    ->columnSpanFull(),

                    Textarea::make('fasilitas')
                    ->label('Fasilitas (Pisahkan dengan koma)')
                    ->placeholder('Contoh: Wifi, AC, TV Kabel, Sarapan')
                    ->helperText('Tulis fasilitas dipisahkan dengan tanda koma (,)')
                    ->columnSpanFull(),

                enter::make(4)
                    ->schema([
                        TextInput::make('max_dewasa')
                            ->numeric()
                            ->default(2)
                            ->label('Max Dewasa'),
                        TextInput::make('max_anak')
                            ->numeric()
                            ->default(1)
                            ->label('Max Anak'),
                        TextInput::make('beds')
                            ->numeric() // Sebaiknya numeric jika input angka, atau text jika "2 King Size"
                            ->label('Jml Kasur'),
                        TextInput::make('baths')
                            ->numeric()
                            ->label('Jml Kamar Mandi'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto')
                    ->label('Foto Kamar')
                    ->visibility('public')
                    ->disk('public')
                    ->square(), // Biar rapi
                
                TextColumn::make('tipe_kamar')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                // Menampilkan Slug di Tabel (Opsional, biar tau linknya apa)
                TextColumn::make('slug')
                    ->label('Link URL')
                    ->color('gray')
                    ->limit(20),

                TextColumn::make('harga') 
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                
                TextColumn::make('capacity')
                    ->label('Kapasitas')
                    ->getStateUsing(fn ($record) => "{$record->max_dewasa} Dewasa, {$record->max_anak} Anak"),
            ])
            ->actions([ // Saya ubah recordActions jadi actions (Filament v3 standar)
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
            'index' => ListKamars::route('/'),
            'create' => CreateKamar::route('/create'),
            'edit' => EditKamar::route('/{record}/edit'),
        ];
    }
}