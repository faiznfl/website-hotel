<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Testimonial; 
use Illuminate\Support\Facades\Auth; // <--- TAMBAHKAN BARIS INI (Import Facade Auth)

class HomeController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::with('user')->latest()->take(7)->get();

        return view('home', [
            'testimonials' => $testimonials,
        ]);
    }

    public function store(Request $request)
    {
        // 0. Keamanan Ekstra: Pakai Auth::check() biar editor tidak merah
        if (!Auth::check()) {
            return back()->with('error', 'Maaf, Anda harus login terlebih dahulu untuk mengirim review.');
        }

        // 1. Validasi Input
        $request->validate([
            'review' => 'required|string|max:500',
            'stars' => 'required|integer|min:1|max:5',
        ]);

        // 2. Simpan ke Database: Pakai Auth::id() biar editor tidak merah
        Testimonial::create([
            'user_id' => Auth::id(), 
            'review'  => $request->review,
            'stars'   => $request->stars,
        ]);

        return redirect()->to(url()->previous() . '#testimoni')
            ->with('success', 'Terima kasih! Review Anda telah berhasil dikirim.');
    }

    public function reviews()
    {
        $reviews = Testimonial::with('user')->latest()->paginate(9);
        
        return view('reviews', compact('reviews'));
    }
}