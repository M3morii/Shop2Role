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

    public function index()
    {
        // Menampilkan semua invoice
        $invoices = Invoice::with('orders')->get();
        return response()->json($invoices);
    }
}
