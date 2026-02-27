<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kamar extends Model
{
    protected $fillable = [
        'tipe_kamar',
        'slug',
        'foto',
        'max_dewasa',
        'max_anak',
        'beds',
        'baths',        
        'harga',
        'deskripsi',
        'fasilitas'
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function galleries()
    {
        return $this->hasMany(Gallery::class);
    }
}
