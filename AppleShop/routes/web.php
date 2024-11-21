<?php

use Illuminate\Support\Facades\Route;
//category
use App\Http\Controllers\Category\CreateCategoriesController;
use App\Http\Controllers\Category\UpdateCategoriesController;
use App\Http\Controllers\Category\GetAllCategoriesController;
use App\Http\Controllers\Category\GetByIdCategoriesController;
//product
use App\Http\Controllers\Product\CreateProductController;
use App\Http\Controllers\Product\UpdateProductController;
use App\Http\Controllers\Product\GetAllProductController;
use App\Http\Controllers\Product\GetByIdProductController;
use App\Http\Controllers\Product\GetByNameProductController;
//Coupon
use App\Http\Controllers\Coupon\CreateCouponController;
use App\Http\Controllers\Coupon\UpdateCouponController;
use App\Http\Controllers\Coupon\GetAllCouponController;
use App\Http\Controllers\Coupon\GetByIdCouponController;


Route::prefix('product')->group(function () {
    Route::get('/get-all', [GetAllProductController::class, 'index']);
    Route::post('/create', [CreateProductController::class, 'store']);
    Route::put('/update/{id}', [UpdateProductController::class, 'update']);
    Route::get('/get-by-name', [GetByNameProductController::class, 'getbyname']);
    Route::get('/get-by-id/{id}', [GetByIdProductController::class, 'show']);
});

Route::prefix('coupon')->group(function () {
    Route::get('/get-all', [GetAllCouponController::class, 'index']);
    Route::post('/create', [CreateCouponController::class, 'store']);
    Route::put('/update/{id}', [UpdateCouponController::class, 'update']);
    //Route::delete('/delete/{id}', [CouponController::class, 'destroy']);
    Route::get('/get-by-id/{id}', [GetByIdCouponController::class, 'show']);
});

Route::prefix('category')->group(function () {
    Route::get('/get-all', [GetAllCategoriesController::class, 'index']);
    Route::post('/create', [CreateCategoriesController::class, 'store']);
    Route::put('/update/{id}', [UpdateCategoriesController::class, 'update']);
    Route::get('/get-by-id/{id}', [GetByIdCategoriesController::class, 'show']);
});
