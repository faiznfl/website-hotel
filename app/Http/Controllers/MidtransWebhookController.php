<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Notification;

class MidtransWebhookController extends Controller
{
    public function handler(Request $request)
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);

        try {
            $notification = new Notification();
            $orderIdRaw = $notification->order_id; 
            $transactionStatus = $notification->transaction_status;

            // 1. LOGIKA UNTUK PESANAN MAKANAN (RESTO)
            if (str_contains($orderIdRaw, 'FOOD-')) {
                $orderIdParts = explode('-', $orderIdRaw);
                $orderId = $orderIdParts[1]; // Ambil angka di tengah

                $order = \App\Models\Order::find($orderId);

                if ($order) {
                    if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                        // Update sesuai status di migrasi resto kamu: 'Lunas'
                        $order->update(['status_pembayaran' => 'Lunas']);
                    } elseif ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
                        $order->update(['status_pembayaran' => 'Dibatalkan']);
                    }
                }
                return response()->json(['message' => 'Food Order Webhook Success']);
            } 

            // 2. LOGIKA UNTUK HOTEL (BOOKING)
            else {
                $orderIdParts = explode('-', $orderIdRaw);
                // Cek apakah ada dash, jika tidak pakai ID asli (tergantung format ID hotel kamu)
                $bookingId = isset($orderIdParts[1]) ? $orderIdParts[1] : $orderIdRaw;

                $booking = Booking::find($bookingId);

                if (!$booking) {
                    return response()->json(['message' => 'Booking not found'], 404);
                }

                // Logika status hotel kamu
                if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                    $booking->update(['status' => 'confirmed']);
                } elseif ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
                    $booking->update(['status' => 'cancelled']);
                }
                
                return response()->json(['message' => 'Hotel Webhook Success']);
            }

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}