<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockLog; // Pastikan Anda memiliki model StockLog

class StockLogController extends Controller
{
    public function index()
    {
        // Mengambil semua log stok
        return StockLog::all();
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'action' => 'required|in:add,subtract', // action bisa ditambah atau dikurangi
            'quantity' => 'required|integer|min:1',
            'user_id' => 'required|exists:users,id',
        ]);

        // Mencatat perubahan stok
        $log = StockLog::create($request->all());

        return response()->json($log, 201);
    }

    public function show($id)
    {
        // Menampilkan detail log stok berdasarkan ID
        return StockLog::findOrFail($id);
    }

    public function destroy($id)
    {
        // Menghapus log stok
        $log = StockLog::findOrFail($id);
        $log->delete();

        return response()->json(['message' => 'Stock log deleted.']);
    }
}
