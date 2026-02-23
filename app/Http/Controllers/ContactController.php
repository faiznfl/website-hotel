<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;

class ContactController extends Controller
{
    public function store(Request $request)
{
    // 1. Validasi input
    $validated = $request->validate([
        'nama'    => 'required|string|max:255',
        'email'   => 'required|email|max:255',
        'phone'   => 'required|string|max:20', // Wajib diisi, maksimal 20 karakter
        'pesan' => 'required|string',
    ]);

    // 2. Simpan ke Database
    Contact::create($validated);

    // 3. Redirect kembali
    return back()->with('success', 'Pesan Anda berhasil dikirim! Kami akan segera menghubungi Anda.');
}
}
