<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RestaurantOrderController;
use App\Http\Controllers\LaporanController; // Pastikan ini ada
use App\Models\Kamar;
use App\Models\Gallery;
use App\Models\Meeting;
use App\Models\Menu;

/*
|--------------------------------------------------------------------------
| 1. HALAMAN PUBLIK
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return redirect('/');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/rooms', function () {
    $rooms = Kamar::all();
    return view('rooms', compact('rooms'));
})->name('rooms.index'); 

Route::get('/gallery', function () {
    $galleries = Gallery::latest()->get();
    return view('gallery', compact('galleries'));
});

Route::get('/meetings-events', function () {
    $meetings = Meeting::latest()->get(); 
    return view('meetings', compact('meetings'));
});

Route::get('/contact', function () {
    return view('contact');
});

Route::get('/rooms/{slug}', function ($slug) {
    $room = Kamar::with('galleries')->where('slug', $slug)->firstOrFail();
    return view('room-detail', compact('room'));
})->name('room.detail');

Route::get('/meetings-events/{slug}', function ($slug) {
    $meeting = Meeting::where('slug', $slug)->firstOrFail();
    return view('meeting-detail', compact('meeting'));
})->name('meeting.detail');

Route::post('/contact-send', [ContactController::class, 'store'])->name('contact.send');
Route::post('/testimoni', [HomeController::class, 'store'])->name('testimoni.store');
Route::get('/reviews', [HomeController::class, 'reviews'])->name('reviews.index');

Route::get('/restaurant', function () {
    $menus = Menu::where('is_available', true)->latest()->get();
    return view('restaurant', compact('menus'));
})->name('restaurant');

// Cetak Laporan
Route::get('/cetak-laporan', [LaporanController::class, 'cetakPdf'])->name('cetak.laporan.pdf');

/*
|--------------------------------------------------------------------------
| 2. API ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/api/check-availability', [BookingController::class, 'checkAvailability'])->name('api.check_availability');

/*
|--------------------------------------------------------------------------
| 3. HALAMAN MEMBER (LOGIN REQUIRED)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    
    // --- RESTAURANT ORDER ---
    Route::post('/restaurant-order/store', [RestaurantOrderController::class, 'store'])->name('restaurant.order.store');

    // --- BOOKING FLOW (URUTAN ALUR) ---
    
    // 1. Halaman Form Booking (Pilih Kamar & Tanggal)
    Route::get('/booking/create', [BookingController::class, 'create'])->name('booking.create');
    
    // 2. Proses Simpan ke Database (Aksi Submit Form)
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');

    // 3. Halaman Pembayaran (TAMPIL SETELAH SIMPAN) [BARU DITAMBAHKAN]
    Route::get('/booking/payment/{id}', [BookingController::class, 'payment'])->name('booking.payment');

    // --- RIWAYAT & MANAJEMEN ---
    
    // List Riwayat Pesanan
    Route::get('/riwayat-pesanan', [BookingController::class, 'history'])->name('booking.history');
    
    // Detail / Invoice Booking
    Route::get('/booking/{id}', [BookingController::class, 'show'])->name('booking.show');
    
    // Batalkan Pesanan
    Route::patch('/booking/{id}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');

    // --- PROFILE ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';