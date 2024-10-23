<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return view('auth.login');
});

Route::get('/Admin', function () {
    return view('admin.index');
});

Route::get('/Customer', function () {
    return view('customer.index');
});
Route::get('/register', function () {
    return view('auth.register');
});


