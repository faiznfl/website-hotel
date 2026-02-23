<?php

namespace App\Filament\Resources\Menus;

use App\Filament\Resources\Menus\Pages\CreateMenu;
use App\Filament\Resources\Menus\Pages\EditMenu;
use App\Filament\Resources\Menus\Pages\ListMenus;
use App\Models\Menu;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = "Menu Restoran";

    protected static ?string $recordTitleAttribute = 'name';
    protected static string | \UnitEnum | null $navigationGroup = 'Data Master Hotel';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // --- KOLOM KIRI: INFORMASI UTAMA ---
                Group::make()
                    ->schema([
                        Section::make('Detail Hidangan')
                            ->description('Masukan informasi dasar mengenai menu makanan/minuman.')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                // Nama Menu
                                TextInput::make('nama')
                                    ->label('Nama Menu')
                                    ->placeholder('Contoh: Nasi Goreng Spesial')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
                                    ->prefixIcon('heroicon-m-pencil-square'),

                                Grid::make(2)->schema([
                                    // Kategori
                                    Select::make('kategori')
                                        ->label('Kategori')
                                        ->options([
                                            'makanan' => 'Makanan',
                                            'minuman' => 'Minuman',
                                            'snack' => 'Cemilan / Dessert',
                                        ])
                                        ->native(false) // Tampilan dropdown lebih modern
                                        ->searchable()
                                        ->required()
                                        ->prefixIcon('heroicon-m-tag'),

                                    // Harga
                                    TextInput::make('harga')
                                        ->label('Harga')
                                        ->placeholder('0')
                                        ->required()
                                        ->numeric()
                                        ->prefix('Rp')
                                        ->minValue(0),
                                ]),

                                // Deskripsi
                                Textarea::make('deskripsi')
                                    ->label('Deskripsi Singkat')
                                    ->placeholder('Jelaskan bahan utama dan rasa hidangan ini...')
                                    ->rows(4)
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                    ])->columnSpan(['lg' => 2]), // Lebar 2 kolom di layar besar

                // --- KOLOM KANAN: MEDIA & PENGATURAN ---
                Group::make()
                    ->schema([
                        Section::make('Media & Status')
                            ->schema([
                                // Upload Gambar
                                FileUpload::make('foto')
                                    ->label('Foto Menu')
                                    ->image()
                                    ->imageEditor()
                                    ->imagePreviewHeight('200')
                                    ->directory('menus')
                                    ->disk('public')
                                    ->preserveFilenames()
                                    ->maxSize(5120) // 5MB
                                    ->columnSpanFull(),

                                // Toggle Ketersediaan
                                Toggle::make('is_available')
                                    ->label('Stok Tersedia?')
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->inline(false)
                                    ->default(true),
                            ]),

                        Section::make('Meta Data')
                            ->schema([
                                // Slug (Read Only tapi terlihat)
                                TextInput::make('slug')
                                    ->label('URL Slug')
                                    ->helperText('Otomatis dibuat dari nama menu.')
                                    ->readOnly()
                                    ->disabled()
                                    ->dehydrated()
                                    ->prefixIcon('heroicon-m-link'),
                            ]),
                    ])->columnSpan(['lg' => 1]), // Lebar 1 kolom di layar besar
            ])
            ->columns(3); // Total Grid Layout adalah 3 Kolom
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Gambar Bulat
                ImageColumn::make('foto')
                    ->label('')
                    ->circular() // Membuat gambar bulat
                    ->visibility('public')
                    ->disk('public')
                    ->size(50),

                // Nama & Slug (Stacked)
                TextColumn::make('nama')
                    ->label('Nama Menu')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Menu $record): string => $record->slug),

                // Kategori Badge
                TextColumn::make('kategori')
                    ->label('Kategori')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'makanan' => 'success', // Hijau
                        'minuman' => 'info',    // Biru
                        'snack' => 'warning',   // Kuning
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'makanan' => 'heroicon-m-cake',
                        'minuman' => 'heroicon-m-beaker',
                        'snack' => 'heroicon-m-sparkles',
                        default => 'heroicon-m-question-mark-circle',
                    }),

                // Harga Bold
                TextColumn::make('harga')
                    ->label('Harga')
                    ->money('IDR', locale: 'id') // Format Rp otomatis
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),

                // Toggle Switch
                ToggleColumn::make('is_available')
                    ->label('Stok')
                    ->onColor('success')
                    ->offColor('danger'),
            ])
            ->filters([
                SelectFilter::make('kategori')
                    ->label('Filter Kategori')
                    ->options([
                        'makanan' => 'Makanan',
                        'minuman' => 'Minuman',
                        'snack' => 'Snack',
                    ]),
            ])
            ->actions([
                EditAction::make()->iconButton(), // Jadi tombol ikon saja biar rapi
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
            'index' => ListMenus::route('/'),
            'create' => CreateMenu::route('/create'),
            'edit' => EditMenu::route('/{record}/edit'),
        ];
    }
}