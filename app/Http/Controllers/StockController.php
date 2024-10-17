<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StockController extends Controller
{
    // Menampilkan semua stok
    public function index()
    {
        $stocks = Stock::all();
        return response()->json($stocks, 200);
    }

    // Menampilkan stok berdasarkan item_id
    public function show($itemId)
    {
        $stock = Stock::where('item_id', $itemId)->first();

        if (!$stock) {
            return response()->json(['message' => 'Stock not found'], 404);
        }

        return response()->json($stock, 200);
    }

    // Fungsi untuk memperbarui stok
    public function updateStock(Request $request)
    {
        // Validasi input
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer',
            'type' => 'required|in:in,out,initial',
        ]);

        // Ambil item dan stock terkait
        $item = Item::find($request->item_id);
        $stock = Stock::where('item_id', $item->id)->first();

        // Tentukan apakah menambah atau mengurangi stock
        if ($request->type === 'in') {
            // Tambah quantity di stock
            $stock->quantity += $request->quantity;

            // Juga tambahkan ke stock dalam items
            $item->stock += $request->quantity; // Pastikan kolom 'stock' ada di tabel items
        } else {
            // Kurangi quantity di stock
            if ($stock->quantity >= $request->quantity) {
                $stock->quantity -= $request->quantity;

                // Juga kurangi dari stock dalam items
                $item->stock -= $request->quantity; // Pastikan kolom 'stock' ada di tabel items
            } else {
                return response()->json(['message' => 'Not enough stock available.'], 400);
            }
        }

        // Simpan perubahan
        $stock->save();
        $item->save(); // Simpan item setelah memperbarui stock

        return response()->json(['message' => 'Stock updated successfully.']);
    }
    public function reduceStock($itemId, $quantity)
{
    // Cari stok berdasarkan item ID
    $stock = Stock::where('item_id', $itemId)->first();

    // Periksa apakah stok ada
    if (!$stock) {
        return response()->json(['message' => 'Stok tidak ditemukan'], 404);
    }

    // Periksa apakah jumlah stok mencukupi
    if ($stock->quantity < $quantity) {
        return response()->json(['message' => 'Stok tidak cukup'], 400);
    }

    // Kurangi jumlah stok
    $stock->quantity -= $quantity;
    $stock->save();

    // Simpan log pengeluaran ke tabel stok
    Stock::create([
        'item_id' => $itemId,
        'quantity' => $quantity,
        'type' => 'out', // Tipe pengeluaran
    ]);

    return response()->json(['message' => 'Stok berhasil dikurangi', 'remaining_stock' => $stock->quantity]);
}

}