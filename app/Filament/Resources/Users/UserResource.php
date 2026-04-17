<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Manajemen Staff & Manager';
   protected static string | \UnitEnum | null $navigationGroup = 'Data Master Hotel';
   protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
        ->schema([
            Section::make()
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),
                    
                    // INPUT ROLE PENTING
                    Select::make('role')
                        ->options([
                            'admin' => 'Administrator',
                            'manager' => 'Manager',
                            'receptionist' => 'Resepsionis',
                        ])
                        ->required()
                        ->native(false),

                    TextInput::make('password')
                        // Label dinamis: Jika konteksnya 'create' jadi "Password", selain itu jadi "Password Baru"
                        ->label(fn (string $context): string => $context === 'create' ? 'Password' : 'Password Baru')
                        ->password()
                        ->required(fn (string $context): bool => $context === 'create')
                        ->dehydrated(fn ($state) => filled($state))
                        ->revealable()
                        ->minLength(8)
                        ->placeholder(fn (string $context): string => 
                            $context === 'create' ? 'Masukkan password' : 'Kosongkan jika tidak ingin ganti password'
                        ),
                ])
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('name')->searchable(),
            TextColumn::make('email')->searchable(),
            BadgeColumn::make('role')
                ->badge() // Mengubah tampilan jadi kotak berwarna (badge)
                ->color(fn (string $state): string => match ($state) {
                    'admin' => 'danger',      // Merah
                    'manager' => 'info',      // Biru muda
                    'receptionist' => 'warning', // Oranye/Kuning
                    default => 'gray',        // Abu-abu (untuk customer/lainnya)
                })
                ->formatStateUsing(fn (string $state): string => ucfirst($state)) // Huruf depan jadi Kapital
                ->sortable(),
            TextColumn::make('created_at')
                ->dateTime('d M Y'),
        ])
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->role === 'admin';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('role', ['admin', 'manager', 'receptionist']);
    }
}
