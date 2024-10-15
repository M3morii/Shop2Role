<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order; // Pastikan Anda memiliki model Order

class OrderController extends Controller
{
    public function index()
    {
        // Mengambil semua pesanan pengguna yang sedang login
        return Order::where('user_id', auth()->id())->get();
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'cart_id' => 'required|exists:carts,id',
        ]);

        // Membuat pesanan baru
        $order = Order::create([
            'user_id' => auth()->id(),
            'cart_id' => $request->cart_id,
            'status' => 'pending', // Status awal pesanan
            // Tambahkan data lain yang diperlukan
        ]);

        return response()->json($order, 201);
    }

    public function show($id)
    {
        // Menampilkan detail pesanan berdasarkan ID
        return Order::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        // Memperbarui status pesanan
        $order = Order::findOrFail($id);
        $order->status = $request->status; // Pastikan status diterima
        $order->save();

        return response()->json($order);
    }

    public function destroy($id)
    {
        // Menghapus pesanan
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json(['message' => 'Order deleted.']);
    }
}
