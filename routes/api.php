<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InvoiceController;

// Rute autentikasi
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

    // Rute publik untuk mengambil daftar barang
    Route::get('/items', [ItemController::class, 'index']);
    Route::get('/stocks', [StockController::class, 'index']);
// Rute admin
Route::group([
    'prefix' => 'admin',
    'middleware' => ['auth:sanctum', 'role:1']
], function() {
    // Item Routes
    // Route::get('/items', [ItemController::class, 'index']);
    Route::get('/items/{id}', [ItemController::class, 'show']);
    Route::post('/items', [ItemController::class, 'store']);
    Route::post('/items/{id}', [ItemController::class, 'update']);
    Route::delete('/items/{id}', [ItemController::class, 'destroy']);

    // Stock Routes
    
    Route::post('/stocks', [StockController::class, 'updateStock']);
    Route::get('/stocks/{itemId}', [StockController::class, 'show']);

    // File Routes
    Route::post('/items/{itemId}/files', [FileController::class, 'store']);
    Route::delete('/files/{id}', [FileController::class, 'destroy']);

    // Invoice Routes
    Route::get('/invoice', [InvoiceController::class, 'index']);
    Route::get('/invoice/{id}', [InvoiceController::class, 'showInvoice']);

    // Order Routes
    Route::get('/orders', [OrderController::class, 'index']);
    Route::put('/orders/{id}/approve', [OrderController::class, 'approve']);
    Route::put('/orders/{id}/decline', [OrderController::class, 'decline']);
});

// Customer routes
Route::group(['middleware' => ['auth:sanctum', 'role:2']], function() {
    // Cart Routes
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'storeOrUpdate']);
    Route::delete('/cart/{id}', [CartController::class, 'removeFromCart']);

    // Order Routes
    Route::get('/order', [OrderController::class, 'index']);
    Route::post('/order', [OrderController::class, 'store']);

    // Invoice Routes
    Route::get('/invoice', [InvoiceController::class, 'index']);
    Route::get('/invoice/{id}', [InvoiceController::class, 'show']);
});
