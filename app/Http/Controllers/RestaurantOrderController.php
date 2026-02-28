<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RestaurantOrderController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'room_number' => 'required|string|max:10',
            'cart' => 'required|array',
            'cart.*.id' => 'required|exists:menus,id',
            'cart.*.qty' => 'required|integer|min:1',
        ]);

        try {
            // Gunakan Transaction agar data aman (kalau gagal satu, gagal semua)
            DB::beginTransaction();

            // 2. Buat Data Order Utama (Struk)
            $order = Order::create([
                'nama_pemesan' => Auth::user()->name,
                'info_pemesan' => 'Kamar ' . $request->room_number, // Simpan nomor kamar
                'status_pembayaran' => 'Belum Bayar', // Default karena belum ada payment gateway
                'total_harga' => 0, // Nanti diupdate setelah hitung item
                'catatan' => 'Pesanan via Website',
            ]);

            $grandTotal = 0;

            // 3. Loop item keranjang dan simpan ke OrderItem
            foreach ($request->cart as $item) {
                $menu = Menu::find($item['id']);
                
                // Hitung subtotal (Harga dari Database x Qty dari Request)
                // PENTING: Jangan ambil harga dari request frontend untuk keamanan
                $subtotal = $menu->harga * $item['qty'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $menu->id,
                    'jumlah' => $item['qty'],
                    'harga_satuan' => $menu->harga,
                    'subtotal' => $subtotal,
                ]);

                $grandTotal += $subtotal;
            }

            // 4. Update Total Harga di Order Utama
            $order->update(['total_harga' => $grandTotal]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Pesanan berhasil dibuat!',
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