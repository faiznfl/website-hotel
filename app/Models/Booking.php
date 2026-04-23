<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Booking extends Model
{
    protected $guarded = []; 

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
    ];

    // 1. RELASI KE USER
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 2. RELASI KE TIPE KAMAR
    public function kamar()
    {
        return $this->belongsTo(Kamar::class, 'kamar_id');
    }

    // 3. RELASI KE UNIT NOMOR KAMAR
    public function roomUnit(): BelongsTo
    {
        return $this->belongsTo(RoomUnit::class, 'room_unit_id');
    }

    // 4. LOGIKA OTOMATIS (GABUNGAN)
    protected static function booted()
    {
        parent::booted();

        // Logika Kode Booking Otomatis
        static::creating(function ($model) {
            if (empty($model->kode_booking)) {
                $model->kode_booking = 'RB-' . strtoupper(Str::random(5));
            }
        });

        // LOGIKA UPDATE STATUS (Paling Ampuh untuk Frontend & Backend)
        static::saved(function ($booking) {
            // Jika booking sukses dan ada unit yang dipilih
            if ($booking->room_unit_id) {
                RoomUnit::where('id', $booking->room_unit_id)
                    ->update(['status' => 'booked']);
            }
        });

        // Balikkan status jika booking dihapus
        static::deleted(function ($booking) {
            if ($booking->room_unit_id) {
                RoomUnit::where('id', $booking->room_unit_id)
                    ->update(['status' => 'available']);
            }
        });

        static::updated(function ($booking) {
            // Jika status berubah menjadi checked_out
            if ($booking->isDirty('status') && $booking->status === 'checked_out') {
                if ($booking->room_unit_id) {
                    RoomUnit::where('id', $booking->room_unit_id)
                        ->update(['status' => 'available']);
                }
            }
        });
    }
}