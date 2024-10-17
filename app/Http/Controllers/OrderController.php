<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Cart;
use App\Models\Stock;
use App\Models\Invoice;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('item')->get();
    
        return response()->json([
            'success' => true,
            'orders' => $orders,
        ]);
    }
    


    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'cart_id' => 'required|array',
        'cart_id.*' => 'exists:carts,id', // Validasi untuk memastikan cart ada
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    // Buat invoice sementara
    $invoice = Invoice::create([
        'total_price' => 0, // Total price akan diupdate setelah order dibuat
        'purchase_date' => now(),
    ]);

    // Looping untuk setiap cart_id yang diberikan
    foreach ($request->cart_id as $cartId) {
        // Cari cart berdasarkan ID
        $cart = Cart::with('item')->find($cartId);

        // Pastikan cart dan item terkait ada
        if (!$cart || !$cart->item) {
            return response()->json(['error' => 'item not found for this cart.'], 404);
        }

        // Ambil harga item dari relasi item
        $itemPrice = $cart->item->sellprice; // Ambil harga dari relasi item
        $totalPrice = $itemPrice * $cart->quantity; // Hitung total harga

        // Buat order baru
        Order::create([
            'user_id' => $cart->user_id,
            'item_id' => $cart->item_id,
            'quantity' => $cart->quantity,
            'invoice_id' => $invoice->id, // Tautkan ke invoice
            'status' => 'pending', // Status default
            'price' => $totalPrice, // Simpan total harga untuk item ini
        ]);
    }

    // Hapus semua cart yang terkait setelah order dibuat
    Cart::whereIn('id', $request->cart_id)->delete();

    // Update total price di invoice
    $totalPriceInvoice = Order::where('invoice_id', $invoice->id)->sum('price');
    $invoice->update(['total_price' => $totalPriceInvoice]);

    // Return response sukses
    return response()->json([
        'message' => 'Order created successfully',
        'invoice_id' => $invoice->id
    ], 201);
}

public function approve($id, Request $request)
{
    // Mengambil order berdasarkan ID
    $order = Order::find($id); 

    if (!$order) {
        return response()->json(['message' => 'Order Not Found'], 404);
    }

    // Validasi status order
    if ($order->status === 'approved') {
        return response()->json(['message' => 'Order sudah di-approve dan tidak bisa di-decline'], 400);
    } elseif ($order->status === 'declined') {
        return response()->json(['message' => 'Order sudah di-decline'], 400);
    }

    // Set status order ke approved
    $order->status = 'approved';
    $order->save();

    // Ambil item dari order
    $items = $order->items; // Ambil data items terkait dengan order

    if (!$items) {
        return response()->json(['message' => 'No items found in the order.'], 400);
    }

    // Inisialisasi StockController untuk mengurangi stok
    $stockController = new StockController();

    foreach ($items as $item) {
        // Ambil id item dan quantity dari order
        $itemId = $item->id; // Pastikan Anda mengambil id item dengan benar
        $quantity = $item->pivot->quantity; // Ambil quantity dari pivot table jika ada

        // Panggil metode untuk mengupdate stok
        $stockController->updateStock(new Request([
            'item_id' => $itemId,
            'quantity' => $quantity,
            'type' => 'out', // Tipe pengurangan
        ]));
    }

    // Update invoice status jika ada
    $invoice = Invoice::find($order->invoice_id);
    if ($invoice) {
        $invoice->status = 'approved';
        $invoice->save();
    }

    return response()->json(['message' => 'Order berhasil di-approve']);
}

    public function decline($id)
    {
        $order = Order::where('user_id',$id)->with('item')->first(); // Include item relation

        if (!$order) {
            return response()->json(['message' => 'Order Not Found']);
        }
        
        $order->status = 'declined';
        $order->save();

        // Hapus invoice terkait saat order ditolak
        $invoice = Invoice::find($order->invoice_id);
        $invoice->delete();

        return response()->json(['message' => 'Order declined successfully and invoice deleted']);
    }
}
