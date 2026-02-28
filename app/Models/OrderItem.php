<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Kembali ke bapaknya (Struk Utama)
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Sambung ke tabel Master Menu (Untuk ambil nama makanan)
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}