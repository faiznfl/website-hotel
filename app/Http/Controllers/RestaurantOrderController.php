<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config; 
use Midtrans\Snap;   

class RestaurantOrderController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi Input (Tambah payment_method)
        $request->validate([
            'room_number' => 'required|string|max:10',
            'payment_method' => 'required|in:cash,online', // Wajib pilih salah satu
            'cart' => 'required|array',
            'cart.*.id' => 'required|exists:menus,id',
            'cart.*.qty' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // 2. Buat Data Order Utama
            $order = Order::create([
                'user_id' => Auth::id(),
                'nama_pemesan' => Auth::user()->name,
                'info_pemesan' => 'Kamar ' . $request->room_number,
                'status_pembayaran' => 'Belum Bayar',
                'metode_pembayaran' => $request->payment_method, // Simpan pilihan user
                'total_harga' => 0, 
                'catatan' => $request->catatan ?? 'Pesanan via Website',
                'expires_at' => now('Asia/Jakarta')->addMinutes(60),
            ]);

            $grandTotal = 0;
            $midtransItems = [];

            // 3. Loop item keranjang
            foreach ($request->cart as $item) {
                $menu = Menu::find($item['id']);
                $subtotal = $menu->harga * $item['qty'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $menu->id,
                    'jumlah' => $item['qty'],
                    'harga_satuan' => $menu->harga,
                    'subtotal' => $subtotal,
                ]);

                // Siapkan data item hanya jika metode pembayaran 'online'
                if ($request->payment_method == 'online') {
                    $midtransItems[] = [
                        'id' => 'MENU-' . $menu->id,
                        'price' => (int) $menu->harga,
                        'quantity' => (int) $item['qty'],
                        'name' => substr($menu->nama, 0, 50),
                    ];
                }

                $grandTotal += $subtotal;
            }

            $order->update(['total_harga' => $grandTotal]);

            // 4. LOGIKA PERCABANGAN PEMBAYARAN
            $snapToken = null;

            if ($request->payment_method == 'online') {
                // Konfigurasi Midtrans
                Config::$serverKey = env('MIDTRANS_SERVER_KEY');
                Config::$isProduction = false;
                Config::$isSanitized = true;
                Config::$is3ds = true;

                $params = [
                    'transaction_details' => [
                        'order_id' => 'FOOD-' . $order->id . '-' . time(),
                        'gross_amount' => (int) $grandTotal,
                    ],
                    'item_details' => $midtransItems,
                    'customer_details' => [
                        'first_name' => Auth::user()->name,
                        'email' => Auth::user()->email,
                    ],
                    'expiry' => [
                        'start_time' => date("Y-m-d H:i:s O"),
                        'unit' => 'minute',
                        'duration' => 60
                    ]
                ];

                $snapToken = Snap::getSnapToken($params);
                $order->update(['snap_token' => $snapToken]);
            }

            DB::commit();

            // 5. Response dinamis berdasarkan metode
            return response()->json([
                'status' => 'success',
                'message' => $request->payment_method == 'online' 
                             ? 'Silakan selesaikan pembayaran online.' 
                             : 'Pesanan berhasil! Silakan bayar tunai saat pesanan tiba.',
                'payment_method' => $request->payment_method,
                'snap_token' => $snapToken,
                'order_id' => $order->id
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}