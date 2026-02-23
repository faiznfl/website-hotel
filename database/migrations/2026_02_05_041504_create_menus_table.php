<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
        $table->id();
        $table->string('nama', 100);          // 100 karakter cukup panjang untuk nama makanan
        $table->string('slug', 100)->unique(); // Samakan dengan nama
        $table->text('deskripsi');      
        $table->integer('harga');             // Integer biasa aman untuk harga (terutama Rupiah)
        $table->string('kategori', 50);       // Kategori cukup 50 (Makanan, Minuman, Dessert, dll)
        $table->string('foto')->nullable();  // Biarkan default 255 untuk panjang URL/path gambar
        $table->boolean('is_available')->default(true); 
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
