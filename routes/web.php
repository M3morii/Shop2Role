<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return view('auth.login');
});

Route::get('/items', function () {
    return view('admin.index');
});

Route::get('/customer', function () {
    return view('customer.index');
});

Route::get('/register', function () {
    return view('auth.register');
});

