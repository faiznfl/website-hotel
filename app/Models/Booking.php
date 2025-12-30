<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // <--- WAJIB ADA: Untuk membuat kode acak

class Booking extends Model
{
    protected $guarded = []; 

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
    ];

    // 1. RELASI KE USER (PENTING)
    // Agar kita tahu booking ini milik akun siapa
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 2. RELASI KE KAMAR
    public function kamar()
    {
        return $this->belongsTo(Kamar::class, 'kamar_id');
    }

    // 3. GENERATE KODE BOOKING OTOMATIS
    // Fungsi ini akan jalan sendiri sesaat sebelum data disimpan ke database
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Jika kode_booking belum diisi, buatkan otomatis
            if (empty($model->kode_booking)) {
                // Hasil contoh: RB-X7Y9Z
                $model->kode_booking = 'RB-' . strtoupper(Str::random(5));
            }
        });
    }
}