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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('kode_booking', 50)->unique()->nullable(); 
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_tamu', 100); 
            $table->string('nomor_hp', 20);
            $table->foreignId('kamar_id')->nullable()->constrained('kamars')->nullOnDelete();
            $table->date('check_in');
            $table->date('check_out');
            $table->integer('jumlah_kamar')->default(1); 
            $table->decimal('total_harga', 15, 2)->default(0); 
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->string('snap_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};