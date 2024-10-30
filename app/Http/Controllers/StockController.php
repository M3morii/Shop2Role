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
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:in,out',
        ]);

        try {
            // Ambil item
            $item = Item::findOrFail($request->item_id);
            
            // Tentukan apakah menambah atau mengurangi stock
            if ($request->type === 'in') {
                $item->stock += $request->quantity;
            } else {
                // Cek apakah stok cukup untuk dikurangi
                if ($item->stock < $request->quantity) {
                    return response()->json([
                        'message' => 'Stok tidak mencukupi untuk pengurangan'
                    ], 400);
                }
                $item->stock -= $request->quantity;
            }

            // Simpan perubahan stok item
            $item->save();

            // Catat riwayat perubahan stok
            Stock::create([
                'item_id' => $request->item_id,
                'quantity' => $request->quantity,
                'type' => $request->type
            ]);

            return response()->json([
                'message' => 'Stok berhasil diperbarui',
                'current_stock' => $item->stock
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat memperbarui stok: ' . $e->getMessage()
            ], 500);
        }
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

    public function purchaseHistory()
    {
        try {
            $history = Stock::with(['item'])
                           ->orderBy('created_at', 'desc')
                           ->get();
                           
            return response()->json($history);
        } catch (\Exception $e) {
            \Log::error('Error loading purchase history: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal memuat riwayat pembelian'
            ], 500);
        }
    }

}
