<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InvoiceController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Admin routes
Route::group([
    'prefix' => 'admin',
    'middleware' => ['auth:sanctum', 'role:1']
], function() {

    Route::get('/items', [ItemController::class, 'index']);
    Route::get('/items/{id}', [ItemController::class, 'show']);
    Route::post('/items', [ItemController::class, 'store']);
    Route::post('/items/{id}', [ItemController::class, 'update']);
    Route::delete('/items/{id}', [ItemController::class, 'destroy']);

    // Stock Routes
    Route::get('/stocks', [StockController::class, 'index']);
    Route::post('/stocks', [StockController::class, 'updateStock']);

    // File Routes
    Route::post('/items/{itemId}/files', [FileController::class, 'store']);
    Route::delete('/files/{id}', [FileController::class, 'destroy']);

    // Routes untuk InvoiceController
    Route::get('/invoices', [InvoiceController::class, 'index']); // Melihat daftar faktur
    Route::get('/invoices/{id}', [InvoiceController::class, 'showInvoice']); // Melihat detail faktur

    // Routes orders (admin)
    Route::get('/orders', [OrderController::class, 'index']); // Untuk admin
    Route::put('/orders/{id}/approve', [OrderController::class, 'approve']);
    Route::put('/orders/{id}/decline', [OrderController::class, 'decline']);
});

// Customer routes
Route::group(['middleware' => ['auth:sanctum', 'role:2']], function() {
    Route::get('/cart/{id}', [CartController::class, 'index']); // Menampilkan keranjang
    Route::post('/cart/{id}', [CartController::class, 'storeOrUpdate']); // Menambah item ke keranjang
    Route::delete('/cart/{id}', [CartController::class, 'removeFromCart']); // Menghapus item dari keranjang

    Route::post('/orders', [OrderController::class, 'store']); // Membuat pesanan
    Route::get('/invoices', [InvoiceController::class, 'index']); // Menampilkan semua invoice
    Route::get('/invoices/{id}', [InvoiceController::class, 'show']); // Menampilkan invoice tertentu

    Route::post('/orders/{id}', [OrderController::class, 'store']); // Untuk customer membuat order
});
