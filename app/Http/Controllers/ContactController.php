<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'  => 'required|string|max:100', // Sesuaikan dengan migrasi (100)
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'pesan' => 'required|string',
        ]);

        // Opsional: Membersihkan input nomor telepon agar hanya angka dan +
        $validated['phone'] = preg_replace('/[^0-9+]/', '', $request->phone);

        Contact::create($validated);

        return back()->with('success', 'Pesan Anda berhasil dikirim!');
    }
}
