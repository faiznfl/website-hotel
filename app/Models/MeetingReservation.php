<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingReservation extends Model
{
    use HasFactory;

    // Kolom yang boleh diisi
    protected $fillable = [
        'meeting_id', 
        'customer_id', 
        'admin_id', 
        'tanggal_booking', 
        'jam_mulai', 
        'jam_selesai', 
        'status'
    ];

    // Relasi ke Katalog Ruangan (Tabel meetings)
    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Meeting::class, 'meeting_id');
    }

    // Relasi ke Pemesan (User dengan role customer)
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    // Relasi ke Admin yang memproses (Activity Log)
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}