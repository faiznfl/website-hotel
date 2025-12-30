<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tamu extends Model
{
    protected $fillable = [
        'username',
        'email',
        'nama',
        'alamat',
        'no_telepon'
    ];
}
