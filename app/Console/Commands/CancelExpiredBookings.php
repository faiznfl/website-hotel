<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking; // Pastikan model Booking di-import
use Carbon\Carbon;

class CancelExpiredBookings extends Command
{
    /**
     * Nama perintah yang akan kamu ketik di terminal.
     */
    protected $signature = 'bookings:cancel-expired';

    /**
     * Deskripsi perintah saat kamu menjalankan php artisan list.
     */
    protected $description = 'Membatalkan reservasi yang sudah melewati batas waktu pembayaran';

    /**
     * Logika utama pembatalan otomatis.
     */
    public function handle()
    {
        // Tambahkan buffer 10 detik untuk toleransi selisih sistem
        $now = \Carbon\Carbon::now('Asia/Jakarta')->addSeconds(10); 

        $expiredBookings = Booking::where('status', 'pending')
            ->where('expires_at', '<=', $now) // Gunakan <= agar lebih pasti
            ->get();

        if ($expiredBookings->isEmpty()) {
            // Log ini akan muncul di terminal schedule:work
            $this->info("Cek jam " . $now->toDateTimeString() . " : Kosong.");
            return;
        }

        foreach ($expiredBookings as $booking) {
            $booking->update(['status' => 'cancelled']);
            $this->warn("Berhasil membatalkan #{$booking->kode_booking}");
        }
        $this->info('Proses selesai.');
    }
}