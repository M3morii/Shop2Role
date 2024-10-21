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
        $cartItems = Cart::where('user_id', $user->id)->with('item')->get();

        return response()->json([
            'success' => true,
            'cart' => $cartItems
        ]);
    }

    // Menambahkan item ke keranjang atau memperbarui kuantitas jika sudah ada
    public function storeOrUpdate(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::firstOrCreate(
            ['user_id' => $user->id, 'item_id' => $request->item_id],
            ['quantity' => 0]
        );

        $cart->quantity += $request->quantity;
        $cart->save();

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil ditambahkan/diupdate di keranjang',
            'cart' => $cart
        ]);
    }

    // Menghapus item dari keranjang
    public function removeFromCart(Request $request, $cartId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = Cart::findOrFail($cartId);

        if ($cartItem->quantity > $request->quantity) {
            $cartItem->quantity -= $request->quantity;
            $cartItem->save();
        } elseif ($cartItem->quantity == $request->quantity) {
            $cartItem->delete();
        } else {
            return response()->json(['message' => 'Quantity to delete exceeds the quantity in cart.'], 400);
        }

        return response()->json(['message' => 'Quantity removed successfully.', 'cartItem' => $cartItem]);
    }
}
