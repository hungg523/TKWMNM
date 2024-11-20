<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CouponController;

Route::prefix('products')->group(function () {
    Route::get('/get-products', [ProductController::class, 'index']);
    Route::post('/create-product', [ProductController::class, 'store']);
    Route::put('/update-product/{id}', [ProductController::class, 'update']);
    Route::delete('/delete-product/{id}', [ProductController::class, 'destroy']);
    Route::get('/get-product-by-id/{id}', [ProductController::class, 'show']);
});

Route::prefix('coupons')->group(function () {
    Route::get('/get-coupons', [CouponController::class, 'index']);
    Route::post('/create-coupon', [CouponController::class, 'store']);
    Route::put('/update-coupon/{id}', [CouponController::class, 'update']);
    Route::delete('/delete-coupon/{id}', [CouponController::class, 'destroy']);
    Route::get('/get-coupon-by-id/{id}', [CouponController::class, 'show']);
});