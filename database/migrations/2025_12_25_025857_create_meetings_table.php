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
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 150); // 150 karakter sudah sangat lega untuk nama ruangan/event
            $table->string('slug', 150)->unique()->nullable(); // Samakan dengan judul
            $table->string('gambar'); // Biarkan default 255 untuk panjang path gambar
            $table->integer('kapasitas'); // DIUBAH: Dari string ke integer biasa
            $table->text('deskripsi'); 
            $table->text('fasilitas')->nullable(); // DIUBAH: Dari string ke text agar muat banyak
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
