<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('shop');
});

Route::get('/shop', function () {
    return view('shop');
});