<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;

Route::get('/get-products', [ProductController::class, 'index']);

Route::post('/create-product', [ProductController::class, 'store']);