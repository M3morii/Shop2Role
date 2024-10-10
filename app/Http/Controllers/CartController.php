<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;

class CartController extends Controller
{

    public function show(Request $request)
    {
        // Ambil cart items untuk user yang sedang login
        $cartItems = Cart::where('user_id', $request->user()->id)->with('item')->get();

        return response()->json($cartItems);
    }
    public function addToCart(Request $request)
{
    // Validasi request
    $request->validate([
        'item_id' => 'required|integer|exists:items,id',
        'quantity' => 'required|integer|min:1',
    ]);

    // Mendapatkan user yang sedang login
    $user = Auth::user();

    // Menggunakan firstOrCreate untuk menambahkan item ke cart
    $cart = Cart::firstOrCreate(
        ['user_id' => $user->id, 'item_id' => $request->item_id], // Cek jika item sudah ada di cart
        ['quantity' => 0] // Jika tidak ada, inisialisasi quantity
    );

    // Update quantity item di cart
    $cart->quantity += $request->quantity;
    $cart->save();

    return response()->json(['message' => 'Item added to cart successfully!', 'cart' => $cart], 200);
}

    public function removeFromCart(Request $request)
    {
        // Validasi request
        $request->validate([
            'item_id' => 'required|integer|exists:items,id',
            'quantity' => 'required|integer|min:1', // Tambahkan jumlah yang ingin dikurangi
        ]);

        // Mendapatkan user yang sedang login
        $user = Auth::user();

        // Mencari item di cart berdasarkan user_id dan item_id
        $cartItem = Cart::where('user_id', $user->id)
            ->where('item_id', $request->item_id)
            ->first();

        // Jika item ditemukan di cart
        if ($cartItem) {
            // Kurangi quantity item
            $cartItem->quantity -= $request->quantity;

            // Jika quantity <= 0, hapus item dari cart
            if ($cartItem->quantity <= 0) {
                $cartItem->delete();
                return response()->json(['message' => 'Item removed from cart successfully!'], 200);
            } else {
                // Jika masih ada quantity tersisa, simpan perubahan
                $cartItem->save();
                return response()->json(['message' => 'Quantity updated successfully!', 'remaining_quantity' => $cartItem->quantity], 200);
            }
        }

        // Jika item tidak ditemukan di cart
        return response()->json(['message' => 'Item not found in cart!'], 404);
    }

}
