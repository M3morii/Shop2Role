<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;

// Rute autentikasi
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function() {
    // Rute publik untuk mengambil daftar barang
    Route::get('/items', [ItemController::class, 'index']);
    Route::get('/items/search', [ItemController::class, 'search']); // Rute baru untuk pencarian
    Route::get('/stocks', [StockController::class, 'index']);
});

// Rute admin
Route::group(['prefix' => 'admin', 'middleware' => ['auth:sanctum', 'role:1']
], function() {
    // Item Routes
    Route::get('/items', [ItemController::class, 'index']);
    Route::get('/items/{id}', [ItemController::class, 'show']);
    Route::post('/items', [ItemController::class, 'store']);
    Route::post('/items/{id}/update', [ItemController::class, 'update']);
    Route::delete('/items/{id}', [ItemController::class, 'destroy']);

    // Stock Routes
    Route::post('/stocks', [StockController::class, 'updateStock']);
    Route::get('/stocks/{itemId}', [StockController::class, 'show']);
    
    // Rute baru untuk purchase history
    Route::get('/purchase-history', [StockController::class, 'purchaseHistory']);

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

    // Dashboard Summary
    Route::get('/dashboard-summary', [AdminDashboardController::class, 'getDashboardSummary']);

    // Category Routes
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    // User Management Routes
    Route::get('/users', [UserController::class, 'index']);
    Route::put('/users/{id}/change-role', [UserController::class, 'changeRole']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::put('/users/{id}', [UserController::class, 'update']);

    // Delete File Routes
    Route::delete('/items/{itemId}/files/{fileId}', [ItemController::class, 'deleteFile']);
});

// Customer routes
Route::group(['middleware' => ['auth:sanctum', 'role:2']], function() {
    // Cart Routes
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'storeOrUpdate']);
    Route::put('/cart/{id}', [CartController::class, 'updateQuantity']);
    Route::delete('/cart/{id}', [CartController::class, 'removeFromCart']);
    
    // Order Routes
    Route::get('/order', [OrderController::class, 'index']);
    Route::post('/order', [OrderController::class, 'store']);

    // Invoice Routes
    Route::get('/invoice', [InvoiceController::class, 'index']);
    Route::get('/invoice/{id}', [InvoiceController::class, 'show']);

    // Rute baru untuk riwayat pesanan
    Route::get('/order-history', [OrderController::class, 'orderHistory']);

    Route::post('/cart/delete-multiple', [CartController::class, 'deleteMultiple']);
});
