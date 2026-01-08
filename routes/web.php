<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookingController;
use App\Models\Kamar;
use App\Models\Gallery;
use App\Models\Meeting;

/*
|--------------------------------------------------------------------------
| 1. HALAMAN PUBLIK (BISA DIAKSES SIAPA SAJA)
|--------------------------------------------------------------------------
*/

// Halaman Utama
Route::get('/', function () {
    return view('home'); 
});

// Redirect dashboard ke Home
Route::get('/dashboard', function () {
    return redirect('/');
})->middleware(['auth', 'verified'])->name('dashboard');

// Halaman Rooms
Route::get('/rooms', function () {
    $rooms = Kamar::all();
    return view('rooms', compact('rooms'));
});

// Halaman Gallery
Route::get('/gallery', function () {
    $galleries = Gallery::latest()->get();
    return view('gallery', compact('galleries'));
});

// Halaman Meeting
Route::get('/meetings-events', function () {
    $meetings = Meeting::latest()->get(); 
    return view('meetings', compact('meetings'));
});

// Halaman Contact
Route::get('/contact', function () {
    return view('contact');
});

// Detail Kamar
Route::get('/rooms/{slug}', function ($slug) {
    $room = Kamar::where('slug', $slug)->firstOrFail();
    return view('room-detail', compact('room'));
})->name('room.detail');

// Detail Meeting
Route::get('/meetings-events/{slug}', function ($slug) {
    $meeting = Meeting::where('slug', $slug)->firstOrFail();
    return view('meeting-detail', compact('meeting'));
})->name('meeting.detail');


/*
|--------------------------------------------------------------------------
| 2. API / AJAX ROUTES (Untuk Javascript)
|--------------------------------------------------------------------------
*/
// Route ini dipanggil oleh Kalender Flatpickr untuk cek tanggal penuh
// Ditaruh di luar middleware auth agar tamu bisa cek tanggal sebelum login
Route::get('/api/check-availability', [BookingController::class, 'checkAvailability'])->name('api.check_availability');


/*
|--------------------------------------------------------------------------
| 3. HALAMAN KHUSUS MEMBER (WAJIB LOGIN)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    
    // --- FITUR HOTEL ---
    
    // Proses Booking (Simpan ke DB)
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');

    // Halaman Riwayat Pesanan
    Route::get('/riwayat-pesanan', [BookingController::class, 'history'])->name('booking.history');

    // Batalkan Pesanan
    Route::patch('/booking/{id}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');

    // --- FITUR BAWAAN BREEZE (PROFILE) ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    // Route untuk melihat detail/invoice booking
    Route::get('/booking/{id}', [BookingController::class, 'show'])->name('booking.show');
});


/*
|--------------------------------------------------------------------------
| 4. AUTH ROUTES (Login, Register, Logout)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';