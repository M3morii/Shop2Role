<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Item;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function getDashboardSummary()
    {
        // Hitung total penjualan
        $totalSales = Order::where('status', 'approved')->sum('price');

        // Format total penjualan ke dalam Rupiah
        $formattedTotalSales = number_format($totalSales, 0, ',', '.');

        // Ambil item dengan stok rendah (misalnya, kurang dari 10)
        $lowStockItems = Item::where('stock', '<', 10)->select('name', 'stock')->get();

        // Ambil 5 pesanan terbaru
        $recentOrders = Order::orderBy('created_at', 'desc')->take(5)->get();

        return response()->json([
            'totalSales' => $formattedTotalSales,
            'lowStockItems' => $lowStockItems,
            'recentOrders' => $recentOrders
        ]);
    }
}
