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
        $table->string('name');           // Nama Menu (misal: Nasi Goreng)
        $table->string('slug')->unique(); // Slug URL (misal: nasi-goreng)
        $table->text('description');      // Deskripsi menu
        $table->integer('price');         // Harga (gunakan integer biar aman)
        $table->string('category');       // Kategori (Makanan, Minuman, dll)
        $table->string('image')->nullable(); // Foto menu
        $table->boolean('is_available')->default(true); // Status ketersediaan
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
