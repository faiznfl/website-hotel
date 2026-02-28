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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pemesan'); // Bisa nama tamu hotel atau pengunjung luar
            $table->string('info_pemesan')->nullable(); // Cth: "Kamar 102" atau "Meja Nomor 5"
            $table->integer('total_harga')->default(0); // Total semua makanan
            $table->enum('status_pembayaran', ['Belum Bayar', 'Lunas'])->default('Belum Bayar');
            $table->text('catatan')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
