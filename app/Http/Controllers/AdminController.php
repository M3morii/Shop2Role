<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role_id != 1) {
            return redirect('/customer')->with('error', 'Unauthorized access.');
        }

        return view('admin.index');
    }

    public function getDashboardData()
    {
        // Implementasikan logika untuk mengambil data dashboard
        return response()->json([
            'latest_items' => Item::latest()->take(3)->get(),
            'latest_orders' => Order::latest()->take(3)->get(),
            'latest_customers' => User::where('role_id', 2)->latest()->take(5)->get(),
            'statistics' => [
                'total_items' => Item::count(),
                'total_orders' => Order::count(),
                'total_customers' => User::where('role_id', 2)->count(),
            ],
        ]);
    }

    public function getItems()
    {
        // Implementasikan logika untuk mengambil daftar item
        return response()->json(Item::all());
    }
}
