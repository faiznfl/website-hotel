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
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Utilities\Set as set;
use App\Filament\Resources\Meetings\Pages\EditMeeting;
use App\Filament\Resources\Meetings\Pages\ListMeetings;
use App\Filament\Resources\Meetings\Pages\CreateMeeting;

class MeetingResource extends Resource
{
    protected static ?string $model = Meeting::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $recordTitleAttribute = 'judul';

    protected static ?string $navigationLabel = 'Meeting & Events';

    // --- TETAP GUNAKAN SCHEMA SESUAI PERMINTAAN ---
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // BAGIAN 1: IDENTITAS (Grid 2 Kolom)
                Section::make('Identitas Ruangan')
                    ->description('Informasi dasar mengenai nama dan kapasitas ruangan.')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('judul')
                                ->label('Nama Ruangan')
                                ->required()
                                ->placeholder('Contoh: Grand Ballroom')
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (set $set, ?string $state) => $set('slug', Str::slug($state))),

                            TextInput::make('slug')
                                ->label('Link URL')
                                ->disabled()
                                ->dehydrated()
                                ->required()
                                ->unique(Meeting::class, 'slug', ignoreRecord: true),
                        ]),

                        TextInput::make('kapasitas')
                            ->label('Kapasitas Penumpang')
                            ->placeholder('Masukan angka saja, contoh: 50')
                            ->numeric() // Validasi angka
                            ->suffix('Pax / Orang') // Pemanis di belakang input
                            ->required()
                            ->columnSpanFull(),
                    ]),

                // BAGIAN 2: MEDIA (Gambar)
                Section::make('Visualisasi')
                    ->schema([
                        FileUpload::make('gambar')
                            ->label('Foto Ruangan')
                            ->image()
                            ->directory('meeting-images')
                            ->disk('public')
                            ->visibility('public')
                            ->required()
                            ->columnSpanFull()
                            ->imageEditor() // Editor crop bawaan
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9') // Rasio wide
                            ->imageResizeTargetWidth('1000')
                            ->imageResizeTargetHeight('560')
                            ->preserveFilenames()
                            ->maxSize(5120), // Max 5MB
                    ]),

                // BAGIAN 3: DETAIL (RichEditor & Fasilitas)
                Section::make('Detail & Fasilitas')
                    ->schema([
                        // GUNAKAN RICH EDITOR (Biar bisa bold/italic)
                        RichEditor::make('deskripsi')
                            ->label('Deskripsi Lengkap')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'bulletList',
                                'orderedList',
                                'undo',
                                'redo',
                            ])
                            ->required()
                            ->columnSpanFull(),

                        Textarea::make('fasilitas')
                            ->label('Fasilitas Tersedia')
                            ->placeholder('Contoh: Wifi High Speed, Projector, Sound System...')
                            ->helperText('Pisahkan setiap fasilitas dengan tanda koma (,).')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Gambar Kotak agak besar
                ImageColumn::make('gambar')
                    ->label('Preview')
                    ->square()
                    ->disk('public')
                    ->visibility('public')
                    ->size(80), 

                // Judul & Slug
                TextColumn::make('judul')
                    ->label('Ruangan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Meeting $record): string => $record->slug ?? '-') // Slug jadi deskripsi kecil
                    ->wrap(),

                // Kapasitas pakai Badge/Warna
                TextColumn::make('kapasitas')
                    ->label('Kapasitas')
                    ->sortable()
                    ->badge() 
                    ->color('info') 
                    ->formatStateUsing(fn (string $state): string => $state . ' Pax'),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date()
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