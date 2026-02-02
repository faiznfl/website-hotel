<?php

namespace App\Filament\Resources\Testimonials;

use BackedEnum;
use Filament\Tables\Table;
use App\Models\Testimonial;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use App\Filament\Resources\Testimonials\Pages\EditTestimonial;
use App\Filament\Resources\Testimonials\Pages\ListTestimonials;
use App\Filament\Resources\Testimonials\Pages\CreateTestimonial;
use App\Filament\Resources\Testimonials\Schemas\TestimonialForm;
use App\Filament\Resources\Testimonials\Tables\TestimonialsTable;

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
                            ->prefixIcon('heroicon-m-user'),

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
                            ->required(),

                        // INPUT KONTEN (Tanpa Upload Foto Lagi)
                        Textarea::make('content')
                            ->label('Isi Review')
                            ->rows(3)
                            ->columnSpanFull()
                            ->required(),
                    ])->columns(2),
            ]);
        // return TestimonialForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // GANTI IMAGE JADI ICON USER BIASA
                TextColumn::make('name')
                    ->label('Nama Tamu')
                    ->icon('heroicon-m-user-circle') // Pengganti foto
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('stars')
                    ->label('Rating')
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => str_repeat('★', (int) $state))
                    ->color('warning'), 

                TextColumn::make('content')
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
            'index' => ListTestimonials::route('/'),
            'create' => CreateTestimonial::route('/create'),
            'edit' => EditTestimonial::route('/{record}/edit'),
        ];
    }
}
