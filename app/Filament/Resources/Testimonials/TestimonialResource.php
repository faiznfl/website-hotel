<?php

namespace App\Filament\Resources\Testimonials;

use App\Filament\Resources\Testimonials\Pages\CreateTestimonial;
use App\Filament\Resources\Testimonials\Pages\EditTestimonial;
use App\Filament\Resources\Testimonials\Pages\ListTestimonials;
use App\Models\Testimonial;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $navigationLabel = "Review";

    protected static string | \UnitEnum | null $navigationGroup = 'Website & Feedback';
    
    protected static ?int $navigationSort = 5;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Detail Testimoni')
                    ->schema([
                        // INPUT NAMA
                        TextInput::make('name')
                            ->label('Nama Tamu')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-m-user')
                            ->readOnly(), // Agar tidak bisa diedit

                        // INPUT BINTANG
                        Select::make('stars')
                            ->label('Rating Bintang')
                            ->options([
                                5 => '⭐⭐⭐⭐⭐ (Sempurna)',
                                4 => '⭐⭐⭐⭐ (Bagus)',
                                3 => '⭐⭐⭐ (Standar)',
                                2 => '⭐⭐ (Kurang)',
                                1 => '⭐ (Buruk)',
                            ])
                            ->default(5)
                            ->required()
                            ->disabled(), // Agar dropdown terkunci (tidak bisa diedit)

                        // INPUT KONTEN
                        Textarea::make('review')
                            ->label('Isi Review')
                            ->rows(3)
                            ->columnSpanFull()
                            ->required()
                            ->readOnly(), // Agar tidak bisa diedit
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // GANTI IMAGE JADI ICON USER BIASA
                TextColumn::make('name')
                    ->label('Nama Tamu')
                    ->icon('heroicon-m-user-circle') 
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('stars')
                    ->label('Rating')
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => str_repeat('★', (int) $state))
                    ->color('warning'),

                TextColumn::make('review')
                    ->label('Isi Review')
                    ->limit(50)
                    ->wrap(),

                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->since() // Tampil: "2 jam yang lalu"
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                ViewAction::make(),   // Hanya bisa melihat detail
                DeleteAction::make(), // Tetap bisa menghapus jika perlu
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
            'index' => ListTestimonials::route('/'),
            'create' => CreateTestimonial::route('/create'),
            // 'edit' => EditTestimonial::route('/{record}/edit'), 
        ];
    }
}