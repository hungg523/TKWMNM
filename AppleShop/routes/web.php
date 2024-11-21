<?php

use Illuminate\Support\Facades\Route;
//category
use App\Http\Controllers\CouponController;
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


Route::prefix('product')->group(function () {
    Route::get('/get-all', [GetAllProductController::class, 'index']);
    Route::post('/create', [CreateProductController::class, 'store']);
    Route::put('/update/{id}', [UpdateProductController::class, 'update']);
    Route::delete('/get-by-name/{string}', [GetByNameProductController::class, 'getbyname']);
    Route::get('/get-by-id/{id}', [GetByIdProductController::class, 'show']);
});

Route::prefix('coupons')->group(function () {
    Route::get('/get-all', [CouponController::class, 'index']);
    Route::post('/create', [CouponController::class, 'store']);
    Route::put('/update/{id}', [CouponController::class, 'update']);
    Route::delete('/delete/{id}', [CouponController::class, 'destroy']);
    Route::get('/get-by-id/{id}', [CouponController::class, 'show']);
});

Route::prefix('category')->group(function () {
    Route::get('/get-all', [GetAllCategoriesController::class, 'index']);
    Route::post('/create', [CreateCategoriesController::class, 'store']);
    Route::put('/update/{id}', [UpdateCategoriesController::class, 'update']);
    Route::get('/get-by-id/{id}', [GetByIdCategoriesController::class, 'show']);
});
