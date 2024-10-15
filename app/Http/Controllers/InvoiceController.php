<?php
namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    // Menyimpan invoice
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'total_price' => 'required|numeric',
            'purchase_date' => 'required|date',
        ]);

        // Membuat invoice baru
        $invoice = Invoice::create([
            'user_id' => $request->user_id,
            'total_price' => $request->total_price,
            'purchase_date' => $request->purchase_date,
        ]);

        return response()->json($invoice, 201);
    }

    // Menampilkan semua invoice
    public function index()
    {
        $invoices = Invoice::with('user')->get(); // Asumsikan Anda memiliki relasi dengan model User
        return response()->json($invoices);
    }

    // Menampilkan invoice tertentu
    public function show($id)
    {
        $invoice = Invoice::with('user')->findOrFail($id); // Asumsikan Anda memiliki relasi dengan model User
        return response()->json($invoice);
    }

    // Menghapus invoice
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete(); // Menghapus invoice
        return response()->json(null, 204);
    }
}
