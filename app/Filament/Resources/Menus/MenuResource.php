<?php

namespace App\Filament\Resources\Menus;

use BackedEnum;
use App\Models\Menu;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Schemas\Components\Utilities\Set;
use App\Filament\Resources\Menus\Pages\EditMenu;
use App\Filament\Resources\Menus\Pages\ListMenus;
use App\Filament\Resources\Menus\Pages\CreateMenu;
use App\Filament\Resources\Menus\Schemas\MenuForm;
use App\Filament\Resources\Menus\Tables\MenusTable;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = "Menu Restoran";

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Menu')->schema([
                    // 1. Nama Menu (Otomatis buat slug saat diketik)
                    TextInput::make('name')
                        ->label('Nama Menu')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                    // 2. Slug (Disembunyikan atau Readonly)
                    TextInput::make('slug')
                        ->required()
                        ->disabled()
                        ->dehydrated(), 

                    // 3. Harga
                    TextInput::make('price')
                        ->label('Harga (IDR)')
                        ->required()
                        ->numeric()
                        ->prefix('Rp'),

                    // 4. Kategori (Dropdown)
                    Select::make('category')
                        ->label('Kategori')
                        ->options([
                            'makanan' => 'Makanan Berat',
                            'minuman' => 'Minuman',
                            'snack' => 'Cemilan / Dessert',
                        ])
                        ->required(),
                    
                    // 5. Deskripsi
                    Textarea::make('description')
                        ->label('Deskripsi Singkat')
                        ->required()
                        ->columnSpanFull(),

                    // 6. Upload Gambar
                    FileUpload::make('image')
                        ->Label('Foto Menu')
                        ->image()
                        ->imageEditor()
                        ->imagePreviewHeight('250')
                        ->disk('public')
                        ->directory('menus')
                        ->preserveFilenames()
                        ->maxSize(10240) // 10MB
                        ->columnSpanFull(),

                    // 7. Status Tersedia
                    Toggle::make('is_available')
                        ->label('Tersedia?')
                        ->default(true),
                ])->columns(2),
            ]);
    
        // return MenuForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tampilkan Gambar Kecil
                ImageColumn::make('image')
                    ->label('Foto')
                    ->visibility('public')
                    ->disk('public')
                    ->square()
                    ->size(60), // Ukuran thumbnail pas,

                // Nama Menu & Kategori
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Menu $record): string => Str::limit($record->description, 30)),

                TextColumn::make('category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'makanan' => 'success',
                        'minuman' => 'info',
                        'snack' => 'warning',
                        default => 'gray',
                    }),

                // Format Harga ke Rupiah
                TextColumn::make('price')
                    ->money('IDR')
                    ->sortable(),

                // Toggle Status Langsung di Tabel
                ToggleColumn::make('is_available')->label('Stok'),
            ])
            ->filters([
                // Filter Kategori
                SelectFilter::make('category')
                    ->options([
                        'makanan' => 'Makanan',
                        'minuman' => 'Minuman',
                        'snack' => 'Snack',
                    ]),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
        // return MenusTable::configure($table);
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
            'index' => ListMenus::route('/'),
            'create' => CreateMenu::route('/create'),
            'edit' => EditMenu::route('/{record}/edit'),
        ];
    }
}
