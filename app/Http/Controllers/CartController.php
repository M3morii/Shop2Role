<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // Menampilkan keranjang pengguna yang sedang login
    public function index()
    {
        $user = Auth::user();
        $cartItems = Cart::where('user_id', $user->id)->with('items')->get();

        return response()->json([
            'success' => true,
            'cart' => $cartItems
        ]);
    }

    // Menambahkan item ke keranjang atau memperbarui kuantitas jika sudah ada
    public function storeOrUpdate(Request $request)
    {
        $user = Auth::user();

        // Validasi request
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Cari atau buat entri keranjang
        $cart = Cart::firstOrCreate(
            ['user_id' => $user->id, 'item_id' => $request->item_id],
            ['quantity' => 0] // Set initial quantity to 0
        );

        // Update quantity
        $cart->quantity += $request->quantity;
        $cart->save();

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil ditambahkan/diupdate di keranjang',
            'cart' => $cart
        ]);
    }

    // Menghapus item dari keranjang
    public function destroy($id)
    {
        $user = Auth::user();
        $cart = Cart::where('id', $id)->where('user_id', $user->id)->first();

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan di keranjang'
            ], 404);
        }

        $cart->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil dihapus dari keranjang'
        ]);
    }
}
