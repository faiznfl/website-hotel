<?php

use App\Http\Controllers\MidtransWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Rute bawaan Laravel (boleh dibiarkan)
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// TAMBAHKAN INI UNTUK WEBHOOK MIDTRANS
// URL nya nanti: domain-kamu.test/api/midtrans-callback
Route::post('/midtrans-callback', [MidtransWebhookController::class, 'handler']);