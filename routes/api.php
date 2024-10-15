<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\StockLogController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Admin routes
Route::group(['middleware' => ['auth:sanctum', 'role:admin']], function() {
    Route::get('/items', [ItemController::class, 'index']);
    Route::get('/items/{id}', [ItemController::class, 'show']);
    Route::post('/items', [ItemController::class, 'storeOrUpdate']);
    Route::put('/items/{id}', [ItemController::class, 'storeOrUpdate']);
    Route::delete('/items/{id}', [ItemController::class, 'destroy']);

    Route::get('/stock-logs', [StockLogController::class, 'index']); // Melihat log stok
    Route::post('/stock-logs', [StockLogController::class, 'createLog']); // Membuat log stok

    // Routes untuk InvoiceController
    Route::get('/invoices', [InvoiceController::class, 'index']); // Melihat daftar faktur
    Route::get('/invoices/{id}', [InvoiceController::class, 'showInvoice']); // Melihat detail faktur
});

// Customer routes
Route::group(['middleware' => ['auth:sanctum', 'role:customer']], function() {
    Route::get('/cart', [CartController::class, 'index']); // Menampilkan keranjang
    Route::post('/cart', [CartController::class, 'store']); // Menambah item ke keranjang
    Route::delete('/cart/{id}', [CartController::class, 'destroy']); // Menghapus item dari keranjang

    Route::post('/orders', [OrderController::class, 'store']); // Membuat pesanan
    Route::get('/invoices', [InvoiceController::class, 'index']); // Menampilkan semua invoice
    Route::get('/invoices/{id}', [InvoiceController::class, 'show']); // Menampilkan invoice tertentu
});
