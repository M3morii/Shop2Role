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
        return response()->json(['success' => true, 'orders' => $orders]);
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart_id' => 'required|array',
            'cart_id.*' => 'exists:carts,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $invoice = $this->createInvoice();

        foreach ($request->cart_id as $cartId) {
            $cart = Cart::with('item')->find($cartId);
            if (!$cart || !$cart->item) {
                return response()->json(['error' => 'Item tidak ditemukan untuk keranjang ini.'], 404);
            }
            $this->createOrder($cart, $invoice->id);
        }

        $this->deleteProcessedCarts($request->cart_id);
        $this->updateInvoiceTotal($invoice);

        return response()->json([
            'message' => 'Order berhasil dibuat',
            'invoice_id' => $invoice->id
        ], 201);
    }

    public function approve($id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order Not Found'], 404);
        }

        if ($order->status === 'approved') {
            return response()->json(['message' => 'Order sudah di-approve dan tidak bisa di-decline'], 400);
        } elseif ($order->status === 'declined') {
            return response()->json(['message' => 'Order sudah di-decline'], 400);
        }

        $order->status = 'approved';
        $order->save();

        if (!$order->item) {
            return response()->json(['message' => 'No items found in the order.'], 400);
        }

        $item = Item::find($order->item_id);
        $this->createStockMutation($order);

        if (!$this->updateStock($item, $order->quantity)) {
            return response()->json(['message' => 'Stock kurang'], 400);
        }

        $this->updateInvoiceStatus($order->invoice_id);
        return response()->json(['message' => 'Order berhasil di-approve']);
    }

    public function decline($id)
    {
        $order = Order::where('user_id', $id)->with('item')->first();
        if (!$order) {
            return response()->json(['message' => 'Order Not Found']);
        }
        
        $order->status = 'declined';
        $order->save();

        $this->deleteRelatedInvoice($order->invoice_id);
        return response()->json(['message' => 'Order declined successfully and invoice deleted']);
    }

    private function createInvoice()
    {
        return Invoice::create(['total_price' => 0, 'purchase_date' => now()]);
    }

    private function createOrder($cart, $invoiceId)
    {
        $totalPrice = $cart->item->sellprice * $cart->quantity;
        Order::create([
            'user_id' => $cart->user_id,
            'item_id' => $cart->item_id,
            'quantity' => $cart->quantity,
            'invoice_id' => $invoiceId,
            'status' => 'pending',
            'price' => $totalPrice,
        ]);
    }

    private function deleteProcessedCarts($cartIds)
    {
        Cart::whereIn('id', $cartIds)->delete();
    }

    private function updateInvoiceTotal($invoice)
    {
        $totalPriceInvoice = Order::where('invoice_id', $invoice->id)->sum('price');
        $invoice->update(['total_price' => $totalPriceInvoice]);
    }

    private function createStockMutation($order)
    {
        Stock::create([
            'item_id' => $order->item_id,
            'quantity' => $order->quantity,
            'type' => 'out'
        ]);
    }

    private function updateStock($item, $quantity)
    {
        if (($item->stock - $quantity) > 0) {
            $item->stock -= $quantity;
            $item->save();
            return true;
        }
        return false;
    }

    private function updateInvoiceStatus($invoiceId)
    {
        $invoice = Invoice::find($invoiceId);
        if ($invoice) {
            $invoice->status = 'approved';
            $invoice->save();
        }
    }

    private function deleteRelatedInvoice($invoiceId)
    {
        Invoice::find($invoiceId)?->delete();
    }
}
