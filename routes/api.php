<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\StockLogController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Admin routes
Route::group(['middleware' => ['auth:sanctum', 'role:1']], function() {

    Route::get('/items', [ItemController::class, 'index']);
    Route::get('/items/{id}', [ItemController::class, 'show']);
    Route::post('/items', [ItemController::class, 'store']);
    Route::post('/items/{id}', [ItemController::class, 'update']);
    Route::delete('/items/{id}', [ItemController::class, 'destroy']);

    // Stock Routes
    Route::get('/stocks', [StockController::class, 'index']);
    Route::get('/stocks/{itemId}', [StockController::class, 'show']);
    Route::post('/stocks', [StockController::class, 'store']);
    Route::put('/stocks/{itemId}', [StockController::class, 'update']);
    Route::delete('/stocks/{itemId}', [StockController::class, 'destroy']);

    // File Routes
    Route::post('/items/{itemId}/files', [FileController::class, 'store']);
    Route::delete('/files/{id}', [FileController::class, 'destroy']);

    Route::get('/stock-logs', [StockLogController::class, 'index']); // Melihat log stok
    Route::post('/stock-logs', [StockLogController::class, 'createLog']); // Membuat log stok

    // Routes untuk InvoiceController
    Route::get('/invoices', [InvoiceController::class, 'index']); // Melihat daftar faktur
    Route::get('/invoices/{id}', [InvoiceController::class, 'showInvoice']); // Melihat detail faktur
});

// Customer routes
Route::group(['middleware' => ['auth:sanctum', 'role:2']], function() {
    Route::get('/cart', [CartController::class, 'index']); // Menampilkan keranjang
    Route::post('/cart', [CartController::class, 'storeOrUpdate']); // Menambah item ke keranjang
    Route::delete('/cart/{id}', [CartController::class, 'destroy']); // Menghapus item dari keranjang

    Route::post('/orders', [OrderController::class, 'store']); // Membuat pesanan
    Route::get('/invoices', [InvoiceController::class, 'index']); // Menampilkan semua invoice
    Route::get('/invoices/{id}', [InvoiceController::class, 'show']); // Menampilkan invoice tertentu
});
