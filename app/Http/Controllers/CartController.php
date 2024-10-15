<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart; // Pastikan Anda memiliki model Cart

class CartController extends Controller
{
    public function index()
    {
        // Mengambil semua item di cart pengguna yang sedang login
        return Cart::where('user_id', auth()->id())->get();
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
        ]);
    
        // Menambahkan item ke cart menggunakan firstOrCreate
        $cartItem = Cart::firstOrCreate(
            ['user_id' => auth()->id(), 'item_id' => $request->item_id],
            ['quantity' => $request->quantity]
        );
    
        // Jika item sudah ada di cart, tambahkan jumlahnya
        if (!$cartItem->wasRecentlyCreated) {
            $cartItem->increment('quantity', $request->quantity);
        }
    
        return response()->json($cartItem, 201);
    }
    

    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Memperbarui jumlah item di cart
        $cartItem = Cart::findOrFail($id);
        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json($cartItem);
    }

    public function destroy($id)
    {
        // Menghapus item dari cart
        $cartItem = Cart::findOrFail($id);
        $cartItem->delete();

        return response()->json(['message' => 'Item removed from cart.']);
    }

    public function clear()
    {
        // Menghapus semua item dari cart pengguna yang sedang login
        Cart::where('user_id', auth()->id())->delete();

        return response()->json(['message' => 'Cart cleared.']);
    }
}
