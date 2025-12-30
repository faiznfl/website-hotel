<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $fillable = [
        'judul',
        'slug',
        'gambar',
        'kapasitas',
        'deskripsi',
        'fasilitas'
    ];

    protected $casts = [
        'fasilitas' => 'array',
    ];
}
