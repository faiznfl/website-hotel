<?php

namespace App\Filament\Resources\Galleries;

use BackedEnum;
use App\Models\Gallery;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Forms\Components\FileUpload;
use Filament\Actions\DeleteAction;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use App\Filament\Resources\Galleries\Pages\EditGallery;
use App\Filament\Resources\Galleries\Pages\CreateGallery;
use App\Filament\Resources\Galleries\Pages\ListGalleries;
use App\Filament\Resources\Galleries\Schemas\GalleryForm;
use App\Filament\Resources\Galleries\Tables\GalleriesTable;
use Filament\Forms\Components\FileUpload as ComponentsFileUpload;

class GalleryResource extends Resource
{
    protected static ?string $model = Gallery::class;

    protected static string|BackedEnum|null $navigationIcon = 'solar-gallery-bold';

    protected static ?string $recordTitleAttribute = 'Gallery';

    protected static ?string $navigationLabel = 'Galeri';

    protected static string | \UnitEnum | null $navigationGroup = 'Website & Feedback';
    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                ComponentsFileUpload::make('gambar')->visibility('public')->disk('public')
                    ->label('Upload Foto')
                    ->image()
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('1:1')
                    ->imageResizeTargetWidth('800')
                    ->imageResizeTargetHeight('800')
                    ->directory('gallery-images') 
                    ->visibility('public')
                    ->disk('public') // <--- INI KUNCINYA
                    ->required()
            ]);
        // return GalleryForm::configure($schema);
    }

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            ImageColumn::make('gambar')
                ->label('Preview Foto')
                ->visibility('public')
                ->disk('public') // <--- INI KUNCINYA
                ->sortable()
                ->size(100) // Ukuran di tabel jangan terlalu besar biar ga lemot
                ->square(),

            TextColumn::make('created_at')
                ->label('Diupload Pada')
                ->dateTime('d M Y, H:i') // Format tanggal biar enak dibaca
                ->sortable(),
        ])
        ->actions([ // GANTI 'recordActions' JADI 'actions'
            EditAction::make(),
            DeleteAction::make(),
        ])
        ->bulkActions([ // GANTI 'toolbarActions' JADI 'bulkActions'
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
