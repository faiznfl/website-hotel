<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kamar; // Import model Kamar

class RoomController extends Controller
{
    // 1. Fungsi untuk menampilkan DAFTAR SEMUA KAMAR (Halaman Rooms & Suites)
    public function index()
    {
        // Menarik semua data kamar dari database
        $rooms = Kamar::orderBy('harga', 'asc')->get(); 
        
        // Asumsi nama file blade Anda adalah 'room.index' atau 'rooms'
        return view('room.index', compact('rooms')); 
    }

    // 2. Fungsi untuk menampilkan DETAIL KAMAR + GALERI
    public function detail($slug)
    {
        // Tarik data kamar sekaligus dengan galerinya (Eager Loading)
        $room = Kamar::with('galleries')->where('slug', $slug)->firstOrFail();
        
        return view('room.detail', compact('room'));
    }
}