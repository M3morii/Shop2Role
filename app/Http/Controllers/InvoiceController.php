<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function show($id)
    {
        // Menampilkan detail invoice berdasarkan ID
        $invoice = Invoice::with('orders')->findOrFail($id);
        return response()->json($invoice);
    }

    public function index(Request $request)
    {
        $invoices = Invoice::with('orders.item')
            ->where('status', 'approved')
            ->get()
            ->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'total_price' => $invoice->total_price,
                    'purchase_date' => $invoice->purchase_date,
                    'status' => $invoice->status,
                    'orders' => $invoice->orders->map(function ($order) {
                        return [
                            'id' => $order->id,
                            'item_name' => $order->item->name,
                            'quantity' => $order->quantity,
                            'price' => $order->price,
                        ];
                    }),
                ];
            });

        return response()->json($invoices);
    }
}
