<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomUnit extends Model
{
    protected $fillable = ['kamar_id', 'nomor_kamar', 'status'];

    public function kamar()
    {
        return $this->belongsTo(Kamar::class, 'kamar_id');
    }
}
