<?php

namespace App\Filament\Resources\Meetings;

use BackedEnum;
use App\Models\Meeting;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Support\Icons\Heroicon;
use Filament\Schemas\Components\Grid;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Utilities\Set as set;
use App\Filament\Resources\Meetings\Pages\EditMeeting;
use App\Filament\Resources\Meetings\Pages\ListMeetings;

// --- 1. TAMBAHKAN IMPORT INI ---
use App\Filament\Resources\Meetings\Pages\CreateMeeting;
use App\Filament\Resources\Meetings\Schemas\MeetingForm;
use App\Filament\Resources\Meetings\Tables\MeetingsTable;
// -------------------------------

class MeetingResource extends Resource
{
    protected static ?string $model = Meeting::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $recordTitleAttribute = 'Meeting';

    protected static ?string $navigationLabel = 'Meeting & Events';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Ruangan')
                    ->schema([
                        Grid::make(2)->schema([
                            // 2. EDIT BAGIAN JUDUL (AUTO SLUG)
                            TextInput::make('judul')
                                ->label('Nama Ruangan')
                                ->required()
                                ->placeholder('Contoh: VIP Meeting Room')
                                ->live(onBlur: true) // Bereaksi saat selesai ketik
                                ->afterStateUpdated(fn (set $set, ?string $state) => $set('slug', Str::slug($state))), // Auto isi slug

                            // 3. TAMBAHKAN INPUT SLUG
                            TextInput::make('slug')
                                ->label('Link URL (Otomatis)')
                                ->disabled()
                                ->dehydrated()
                                ->required()
                                ->unique(Meeting::class, 'slug', ignoreRecord: true),

                            // Kapasitas
                            TextInput::make('kapasitas')
                                ->label('Kapasitas')
                                ->required()
                                ->placeholder('Contoh: Max: 20 Pax'),
                        ]),

                        // Upload Gambar
                        FileUpload::make('gambar')
                            ->label('Foto Ruangan')
                            ->image()
                            ->directory('meeting-images')
                            ->required()
                            ->columnSpanFull()
                            ->imageEditor() // Fitur crop bawaan filament
                            ->imageResizeMode('cover') // Resize agar ringan
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeTargetWidth('800')
                            ->imageResizeTargetHeight('450')
                            ->preserveFilenames()
                            ->maxSize(10240)
                            ->visibility('public')
                            ->disk('public'),

                        // Deskripsi
                        Textarea::make('deskripsi')
                            ->label('Deskripsi Singkat')
                            ->rows(3)
                            ->required()
                            ->columnSpanFull(),

                        // 4. UBAH FASILITAS JADI TEXTAREA (Bukan TagsInput)
                        // Agar tersimpan sebagai string biasa: "Wifi, AC, Projector"
                        Textarea::make('fasilitas')
                            ->label('Fasilitas')
                            ->placeholder('Contoh: Wifi, AC, Projector, Sound System')
                            ->helperText('Pisahkan setiap fasilitas dengan tanda koma (,)')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('gambar')
                    ->label('Foto')
                    ->square()
                    ->visibility('public')
                    ->disk('public')
                    ->sortable(),
                
                TextColumn::make('judul')
                    ->searchable()
                    ->weight('bold')
                    ->sortable(),
                
                // Tampilkan slug biar admin tau link-nya
                TextColumn::make('slug')
                    ->label('Link URL')
                    ->color('gray')
                    ->limit(20),

                TextColumn::make('kapasitas')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
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
            'index' => ListMeetings::route('/'),
            'create' => CreateMeeting::route('/create'),
            'edit' => EditMeeting::route('/{record}/edit'),
        ];
    }
}