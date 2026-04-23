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
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('kamar_id')->nullable()->constrained('kamars')->nullOnDelete();
            $table->foreignId('room_unit_id')->nullable()->constrained('room_units')->nullOnDelete();
            $table->string('kode_booking', 15)->unique()->nullable(); 
            $table->string('nama_tamu', 30);
            $table->string('nomor_hp', 15);
            $table->date('check_in');
            $table->date('check_out');
            $table->integer('jumlah_kamar')->default(1); 
            $table->decimal('total_harga', 15, 2)->default(0); 
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'checked_out'])->default('pending');
            $table->string('snap_token')->nullable();
            $table->dateTime('expires_at')->nullable();
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