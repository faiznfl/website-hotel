<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = ['kamar_id', 'foto', 'keterangan'];

    public function kamar()
    {
        return $this->belongsTo(Kamar::class);
    }
}