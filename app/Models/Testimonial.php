<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
    'name', 
    'stars', 
    'review', // <--- Ganti 'content' jadi 'review' di sini
];
}
