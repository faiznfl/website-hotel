<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Testimonial; // <--- Wajib Import Model Ini

class HomeController extends Controller
{
    /**
     * Menampilkan halaman depan (Homepage)
     */
    public function index()
    {
        // 1. Ambil data Testimonial (3 terbaru)
        // Jika model Testimonial belum ada datanya, dia akan mengembalikan array kosong (aman, tidak error)
        $testimonials = Testimonial::latest()->take(5)->get();

        // 2. Kirim data ke View 'home'
        return view('home', [
            'testimonials' => $testimonials,
        ]);
    }

    public function store(Request $request)
    {
        // 1. Validasi Input (Hanya Nama, Review, Bintang)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string|max:500',
            'stars' => 'required|integer|min:1|max:5',
        ]);

        // 2. Simpan ke Database (Tanpa Foto)
        Testimonial::create($validated);

        // 3. Kembali ke Home dengan Pesan Sukses
        return redirect()->to(url()->previous() . '#testimoni')
            ->with('success', 'Terima kasih! Review Anda telah berhasil dikirim.');
    }
}