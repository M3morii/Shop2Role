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
    public function index(Request $request)
    {
        try {
            $query = Order::with(['user', 'item'])
                         ->orderBy('created_at', 'desc');

            // Filter berdasarkan status
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            $orders = $query->get();

            return response()->json([
                'success' => true,
                'orders' => $orders
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading orders: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data pesanan'
            ], 500);
        }
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
            return response()->json(['message' => 'Order sudah disetujui sebelumnya'], 400);
        } elseif ($order->status === 'declined') {
            return response()->json(['message' => 'Order sudah ditolak dan tidak bisa disetujui'], 400);
        }

        // Lanjutkan dengan proses persetujuan jika status masih 'pending'
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
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order Not Found'], 404);
        }

        if ($order->status === 'approved') {
            return response()->json(['message' => 'Order sudah disetujui dan tidak bisa ditolak'], 400);
        } elseif ($order->status === 'declined') {
            return response()->json(['message' => 'Order sudah ditolak sebelumnya'], 400);
        }

        // Lanjutkan dengan proses penolakan jika status masih 'pending'
        $order->status = 'declined';
        $order->save();

        $this->deleteRelatedInvoice($order->invoice_id);
        return response()->json(['message' => 'Order berhasil ditolak dan invoice dihapus']);
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

    public function orderHistory()
    {
        $user = auth()->user();
        $orders = Order::with(['item', 'invoice'])
                       ->whereHas('invoice', function($query) use ($user) {
                           $query->where('user_id', $user->id);
                       })
                       ->orderBy('created_at', 'desc')
                       ->get();

        $formattedOrders = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'invoice_id' => $order->invoice_id,
                'item_name' => $order->item->name,
                'quantity' => $order->quantity,
                'price' => $order->price,
                'status' => $order->invoice->status,
                'date' => $order->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json(['order_history' => $formattedOrders], 200);
    }
}
