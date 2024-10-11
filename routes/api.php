<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CartController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum', 'role:admin']], function () {
    Route::get('/items', [ItemController::class, 'index']);
    Route::post('/items', [ItemController::class, 'store']);
    Route::post('/items/{id}', [ItemController::class, 'update']);
    Route::delete('/items/{id}', [ItemController::class, 'destroy']);
});

Route::group(['middleware' => ['auth:sanctum', 'role:customer']], function () {
    Route::get('/cart', [CartController::class, 'show']);
    Route::post('/cart', [CartController::class, 'addToCart']);
    Route::delete('/cart/{id}', [CartController::class, 'removeFromCart']);
});