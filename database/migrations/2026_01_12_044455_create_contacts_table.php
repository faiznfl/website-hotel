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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);       // 100 karakter sudah standar baku untuk nama
            $table->string('email', 255);      // Biarkan 255 sesuai standar maksimal format email
            $table->string('phone', 20);       // 20 karakter pas untuk nomor HP (+62...)
            $table->text('pesan');           // Tipe text sangat tepat agar tamu bebas nulis panjang
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
