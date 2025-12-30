<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// 1. IMPORT CLASS FILAMENT
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

// 2. TAMBAHKAN 'implements FilamentUser'
class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // <--- PENTING: Agar kolom role bisa diisi
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // 3. LOGIKA SATPAM (Hanya Admin yang Boleh Masuk Filament)
    public function canAccessPanel(Panel $panel): bool
    {
        // Jika role adalah 'admin', silakan masuk.
        // Jika bukan (misal 'user'), DITOLAK (Access Denied).
        return $this->role === 'admin';
    }
}