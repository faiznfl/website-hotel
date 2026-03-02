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
        // Konfigurasi Midtrans
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);

        try {
            $notification = new Notification();
            
            // Ambil Order ID (Contoh: BOOKING-12-171000)
            // Kita ambil ID booking-nya saja (angka di tengah)
            $orderIdRaw = $notification->order_id; 
            $orderIdParts = explode('-', $orderIdRaw);
            $bookingId = $orderIdParts[1]; // Mengambil angka '12'

            $booking = Booking::find($bookingId);

            if (!$booking) {
                return response()->json(['message' => 'Booking not found'], 404);
            }

            $transactionStatus = $notification->transaction_status;
            $type = $notification->payment_type;
            $fraud = $notification->fraud_status;

            // Logika Perubahan Status
            if ($transactionStatus == 'capture') {
                if ($type == 'credit_card') {
                    if ($fraud == 'challenge') {
                        $booking->update(['status' => 'pending']);
                    } else {
                        $booking->update(['status' => 'confirmed']);
                    }
                }
            } elseif ($transactionStatus == 'settlement') {
                $booking->update(['status' => 'confirmed']);
            } elseif ($transactionStatus == 'pending') {
                $booking->update(['status' => 'pending']);
            } elseif ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
                $booking->update(['status' => 'cancelled']);
            }

            return response()->json(['message' => 'Webhook success']);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}