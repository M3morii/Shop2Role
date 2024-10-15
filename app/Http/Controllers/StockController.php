<?php

namespace App\Http\Controllers;

use App\Models\Stock;
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

    // Fungsi untuk menambah stok
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer',
            'buyprice' => 'required',
            'finalstock' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $stock = Stock::create($request->only('item_id', 'quantity','buyprice','finalstock'));

        return response()->json(['message' => 'Stock created successfully', 'stock' => $stock], 201);
    }

    // Fungsi untuk memperbarui stok
    public function update(Request $request, $itemId)
    {
        $stock = Stock::where('item_id', $itemId)->first();

        if (!$stock) {
            return response()->json(['message' => 'Stock not found'], 404);
        }

        $stock->update($request->only('quantity'));

        return response()->json(['message' => 'Stock updated successfully', 'stock' => $stock], 200);
    }

    // Fungsi untuk menghapus stok
    public function destroy($itemId)
    {
        $stock = Stock::where('item_id', $itemId)->first();

        if (!$stock) {
            return response()->json(['message' => 'Stock not found'], 404);
        }

        if ($stock->delete()) {
            return response()->json(['message' => 'Stock deleted successfully'], 200);
        }

        return response()->json(['message' => 'Error deleting stock'], 500);
    }
}
