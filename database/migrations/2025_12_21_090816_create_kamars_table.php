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
        Schema::create('kamars', function (Blueprint $table) {
            $table->id();
            $table->enum('tipe_kamar', ['Superior Room', 'Deluxe Room', 'Family Room']);
            $table->string('slug')->unique();
            $table->string('foto')->nullable();
            $table->string('max_dewasa');
            $table->string('max_anak');
            $table->string('beds');
            $table->integer('baths');
            $table->integer('harga');
            $table->text('deskripsi')->nullable();
            $table->text('fasilitas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kamars');
    }
};
