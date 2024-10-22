<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    // Menampilkan keranjang pengguna yang sedang login
    public function index()
    {
        $user = Auth::user();
        $cartItems = Cart::where('user_id', $user->id)->with('item')->get();

        $formattedCartItems = $cartItems->map(function ($cartItem) {
            return [
                'id' => $cartItem->id,
                'item_name' => $cartItem->item->name,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->item->sellprice,
            ];
        });

        return response()->json($formattedCartItems);
    }

    // Menambahkan item ke keranjang atau memperbarui kuantitas jika sudah ada
    public function storeOrUpdate(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $item = Item::findOrFail($request->item_id);

        // Periksa stok yang tersedia
        $currentCartQuantity = Cart::where('user_id', $user->id)
                                   ->where('item_id', $request->item_id)
                                   ->sum('quantity');

        $availableStock = $item->stock - $currentCartQuantity;

        if ($request->quantity > $availableStock) {
            return response()->json([
                'success' => false,
                'message' => "Stok tidak mencukupi. Stok tersedia: {$availableStock}"
            ], 400);
        }

        $cart = Cart::updateOrCreate(
            ['user_id' => $user->id, 'item_id' => $request->item_id],
            ['quantity' => \DB::raw('quantity + ' . $request->quantity)]
        );

        return response()->json([
            'success' => true,
            'message' => "{$item->name} berhasil ditambahkan ke keranjang",
            'cart' => $cart
        ]);
    }

    // Mengupdate kuantitas item di keranjang
    public function updateQuantity(Request $request, $cartId)
    {
        $cartItem = Cart::findOrFail($cartId);
        
        if ($cartItem->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $newQuantity = $request->quantity;
        $availableStock = $cartItem->item->stock;

        if ($newQuantity > $availableStock) {
            return response()->json([
                'success' => false,
                'message' => "Stok tidak mencukupi. Stok tersedia: {$availableStock}"
            ], 400);
        }

        $cartItem->quantity = $newQuantity;
        $cartItem->save();

        return response()->json([
            'success' => true,
            'message' => "Jumlah {$cartItem->item->name} berhasil diupdate"
        ]);
    }

    // Menghapus item dari keranjang
    public function removeFromCart($cartId)
    {
        $cartItem = Cart::findOrFail($cartId);
        
        if ($cartItem->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $itemName = $cartItem->item->name;
        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => "{$itemName} berhasil dihapus dari keranjang"
        ]);
    }
}
