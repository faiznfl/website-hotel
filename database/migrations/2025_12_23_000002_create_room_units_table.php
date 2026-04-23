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
    // 1. Buat kerangka tabelnya dulu
    Schema::create('room_units', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('kamar_id'); 
        $table->string('nomor_kamar')->unique();
        $table->enum('status', ['available', 'booked', 'maintenance'])->default('available');
        $table->timestamps();
    });

    // 2. Pasang relasi setelah kerangkanya jadi
    Schema::table('room_units', function (Blueprint $table) {
        $table->foreign('kamar_id')->references('id')->on('kamars')->onDelete('cascade');
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_units');
    }
};